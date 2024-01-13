<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\{
    Potongan,
    Prodi,
    TahunAjaran,
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PotonganController extends Controller
{
    public function index()
    {
        $prodis = Prodi::all();
        $tahun_ajarans = TahunAjaran::all();
        return view('data_master.potongan.index', compact('prodis', 'tahun_ajarans'));
    }

    public function data()
    {
        $datas = Potongan::when(request('semester'), function ($q) {
            $q->where('semester_id', request('semester'));
        })->when(request('prodi'), function ($q) {
            $q->where('prodi_id', request('prodi'));
        })->when(request('tahun_ajaran'), function ($q) {
            $q->where('tahun_ajaran_id', request('tahun_ajaran'));
        })->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . "<button class='btn btn-primary mx-2' onclick='detailPotongan(" . $data->id . ")'>Detail</button>";

            if (auth()->user()->can('edit_potongan')) {
                $options = $options . "<a href='" . route('data-master.potongan.edit', $data->id) . "' class='btn btn-warning mx-2'>Edit</a>";
            }

            if (auth()->user()->can('delete_potongan')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteData(`" . route('data-master.potongan.destroy', $data->id) . "`)'>
                                    Hapus
                                </button>";
            }

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('prodi', function ($datas) {
                return $datas->prodi->nama;
            })
            ->addColumn('semester', function ($datas) {
                return $datas->semester->nama;
            })
            ->addColumn('tahun_ajaran', function ($datas) {
                return $datas->tahunAjaran->nama;
            })
            ->editColumn('nominal', function ($datas) {
                return formatRupiah($datas->nominal);
            })
            ->rawColumns(['options'])
            ->make(true);
    }

    public function create()
    {
        $prodis = Prodi::all();
        $tahun_ajarans = TahunAjaran::all();
        return view('data_master.potongan.form', compact('prodis', 'tahun_ajarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nominal' => 'required',
            'ket' => 'required',
            'prodi_id' => 'required',
            'tahun_ajaran_id' => 'required',
            'semester_id' => 'required',
        ]);

        Potongan::create($request->all());

        return redirect()->route('data-master.potongan.index')->with('success', 'Berhasil ditambahkan');
    }

    public function show(Potongan $potongan)
    {
        $potongan->load('prodi', 'tahunAjaran', 'semester');
        return response()->json([
            'message' => 'success',
            'data' => $potongan
        ], 200);
    }

    public function edit(Potongan $potongan)
    {
        $prodis = Prodi::all();
        $tahun_ajarans = TahunAjaran::all();
        $total_mhs = $potongan->mahasiswa->count();
        $semester = $potongan->prodi->semester;
        return view('data_master.potongan.form', [
            'prodis' => $prodis,
            'tahun_ajarans' => $tahun_ajarans,
            'data' => $potongan,
            'total_mhs' => $total_mhs,
            'semester' => $semester
        ]);
    }

    public function update(Request $request, Potongan $potongan)
    {
        $total_mhs = $potongan->mahasiswa->count();

        $validate = [
            'nama' => 'required',
            'nominal' => 'required',
            'ket' => 'required'
        ];

        if ($total_mhs < 1) {
            $validate += [
                'prodi_id' => 'required',
                'tahun_ajaran_id' => 'required',
                'semester_id' => 'required',
            ];
        }

        $request->validate($validate);
        $potongan->update($request->all());

        return redirect()->route('data-master.potongan.index')->with('success', 'Berhasil diubah');
    }

    public function destroy(Potongan $potongan)
    {
        DB::beginTransaction();
        try {
            $potongan->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal dihapus');
        }
    }

    public function getSemester($prodi_id)
    {
        $prodi = Prodi::find($prodi_id);

        if (!$prodi) {
            return response()->json([
                'message' => 'Maaf telah terjadi kesalahan'
            ], 400);
        }

        return response()->json([
            'message' => 'success',
            'data' => $prodi->semester
        ], 200);
    }
}
