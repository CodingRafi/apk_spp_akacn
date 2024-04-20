<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Jenjang;
use App\Models\Kurikulum;
use App\Models\Potongan;
use App\Models\Prodi;
use App\Models\Rombel;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProdiController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_prodi', ['only' => ['index', 'data', 'show']]);
        $this->middleware('permission:add_prodi', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_prodi', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_prodi', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('data_master.prodi.index');
    }

    public function data()
    {
        $datas = Prodi::all();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . "<a href='" . route('data-master.prodi.show', $data->id) . "' class='btn btn-info mx-2'>Detail</a>";

            if (auth()->user()->can('edit_prodi')) {
                $options = $options . "<a href='" . route('data-master.prodi.edit', $data->id) . "' class='btn btn-warning mx-2'>Edit</a>";
            }

            if (auth()->user()->can('delete_prodi')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.prodi.destroy', $data->id) . "`)'>
                                        Hapus
                                    </button>";
            }
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('jenjang', function ($datas) {
                return $datas->jenjang->nama;
            })
            ->editCOlumn('status', function ($datas) {
                return $datas->status ? "<i class='bx bx-check text-success'></i>" : "<i class='bx bx-x text-danger'></i>";
            })
            ->rawColumns(['options', 'status'])
            ->make(true);
    }

    public function create()
    {
        $jenjangs = Jenjang::all();
        return view('data_master.prodi.form', compact('jenjangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'kode' => 'required|unique:prodi',
            'akreditas' => 'required',
            'jenjang_id' => 'required',
        ]);

        Prodi::create([
            'id' => generateUuid(),
            'kode' => $request->kode,
            'nama' => $request->nama,
            'akreditas' => $request->akreditas,
            'jenjang_id' => $request->jenjang_id,
            'status' => $request->status ?? '0'
        ]);

        return redirect()->route('data-master.prodi.index')
            ->with('success', 'Berhasil ditambahkan');
    }

    public function show(Prodi $prodi)
    {
        return view('data_master.prodi.show', compact('prodi'));
    }

    public function angkatan($prodi_id)
    {
        $datas = TahunAjaran::all();
        foreach ($datas as $data) {
            $options = '';
            $options = $options . "<a href='" . route('data-master.prodi.angkatan.detail', ['prodi_id' => $prodi_id, 'tahun_ajaran_id' => $data->id]) . "' class='btn btn-info mx-2'>Detail</a>";
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function angkatanDetail($prodi_id, $tahun_ajaran_id)
    {
        $prodi = Prodi::where('id', $prodi_id)->count();
        $tahun_ajaran = TahunAjaran::where('id', $tahun_ajaran_id)->count();

        if ($prodi < 1 || $tahun_ajaran < 1) {
            abort(404);
        }

        $semesterPotongan = DB::table('semesters')
            ->select('tahun_semester.id', 'semesters.nama')
            ->join('tahun_semester', 'tahun_semester.semester_id', 'semesters.id')
            ->where('tahun_semester.prodi_id', $prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $tahun_ajaran_id)
            ->get();

        $lainnyaPotongan = DB::table('tahun_pembayaran_lain')
            ->select('tahun_pembayaran_lain.*', 'pembayaran_lainnyas.nama')
            ->join('pembayaran_lainnyas', 'pembayaran_lainnyas.id', 'tahun_pembayaran_lain.pembayaran_lainnya_id')
            ->where('tahun_pembayaran_lain.prodi_id', $prodi_id)
            ->where('tahun_pembayaran_lain.tahun_ajaran_id', $tahun_ajaran_id)
            ->get();

        $jenisAktivitas = DB::table('jenis_aktivitas')
            ->get();

        $tahun_semester_id = DB::table('semesters')
            ->where('tahun_ajaran_id', $tahun_ajaran_id)
            ->get()
            ->map(function ($data) {
                return "id_semester='{$data->id}'";
            })
            ->implode(' or ');

        return view('data_master.prodi.angkatan.index', compact(
            'prodi_id',
            'tahun_ajaran_id',
            'semesterPotongan',
            'lainnyaPotongan',
            'jenisAktivitas',
            'tahun_semester_id'
        ));
    }

    public function edit($id)
    {
        $data = Prodi::findOrFail($id);
        $jenjangs = Jenjang::all();
        return view('data_master.prodi.form', compact('data', 'jenjangs'));
    }

    public function update(Request $request, Prodi $prodi)
    {
        $request->validate([
            'nama' => 'required',
            'kode' => 'required|unique:prodi,id,' . $prodi->id,
            'akreditas' => 'required',
            'jenjang_id' => 'required',
        ]);
        $prodi->update([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'akreditas' => $request->akreditas,
            'jenjang_id' => $request->jenjang_id,
            'status' => $request->status ?? '0'
        ]);
        return redirect()->route('data-master.prodi.index')->with('success', 'Berhasil diubah');
    }

    public function destroy(Prodi $prodi)
    {
        DB::beginTransaction();
        try {
            $prodi->delete();
            DB::commit();
            return response()->json([
                'message' => 'Berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal dihapus',
            ], 400);
        }
    }

    public function storeNeoFeeder(Request $request)
    {
        foreach ($request->data as $data) {
            DB::table('prodi')->updateOrInsert([
                'id' => $data['id_prodi'],
            ], [
                'kode' => $data['kode_program_studi'],
                'nama' => $data['nama_program_studi'],
                'akreditas' => $data['status'],
                'jenjang_id' => $data['id_jenjang_pendidikan'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }
}
