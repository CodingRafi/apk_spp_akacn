<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TahunAjaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_tahun_ajaran', ['only' => ['index', 'store']]);
        $this->middleware('permission:add_tahun_ajaran', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_tahun_ajaran', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_tahun_ajaran', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('data_master.tahun_ajaran.index');
    }

    public function data()
    {
        $datas = TahunAjaran::all();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_tahun_ajaran')) {
                $options = $options . "<a href='" . route('data-master.tahun-ajaran.edit', $data->id) . "' class='btn btn-warning mx-2'>Edit</a>";
            }

            if (auth()->user()->can('delete_tahun_ajaran')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteData(`" . route('data-master.tahun-ajaran.destroy', $data->id) . "`)'>
                                        Hapus
                                    </button>";
            }
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editCOlumn('status', function ($datas) {
                return $datas->status ? "<i class='bx bx-check text-success'></i>" : "<i class='bx bx-x text-danger'></i>";
            })
            ->rawColumns(['options', 'status'])
            ->make(true);
    }

    public function create()
    {
        return view('data_master.tahun_ajaran.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_mulai' => 'required|unique:tahun_ajarans,id',
            'tgl_akhir' => 'required|after:tgl_mulai',
            'nama' => 'required'
        ], [
            'tahun_mulai' => 'Tahun mulai sudah digunakan',
        ]);

        //? Validasi tanggal mulai
        $cek = DB::table('tahun_ajarans')
                    ->where('id', explode('-', $request->tgl_mulai)[0])
                    ->count();

        if ($cek > 0) {
            return response()->json([
                'message' => 'Tahun Ajaran ini sudah ada'
            ], 400);
        }
        
        if (getTahunAjaranActive()) {
            return response()->json([
                'message' => 'ada tahun ajaran yang sedang aktif'
            ], 400);
        }

        TahunAjaran::create([
            'id' => explode('-', $request->tgl_mulai)[0],
            'nama' => $request->nama,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_akhir' => $request->tgl_akhir,
            'status' => $request->status ? "1" : "0",
        ]);

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }

    public function edit($id)
    {
        $data = TahunAjaran::findOrFail($id);
        return view('data_master.tahun_ajaran.form', compact('data'));
    }

    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $request->validate([
            'kode' => 'required',
            'tahun_mulai' => 'required|digits:4',
            'tahun_akhir' => 'required|digits:4',
            'semester' => 'required|digits:1'
        ]);

        if ($request->status) {
            $tahun_active = getTahunAjaranActive();
            if ($tahun_active->id !== $tahunAjaran->id) {
                return redirect()->back()
                    ->with(['error' => 'ada tahun ajaran yang sedang aktif'])
                    ->withInput();
            }
        }

        $tahunAjaran->update([
            'kode' => $request->kode,
            'tahun_mulai' => $request->tahun_mulai,
            'tahun_akhir' => $request->tahun_akhir,
            'semester' => $request->semester,
            'status' => $request->status ? "1" : "0",
        ]);
        return redirect()->route('data-master.tahun-ajaran.index')->with('success', 'Berhasil diubah');
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        DB::beginTransaction();
        try {
            $tahunAjaran->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal dihapus');
        }
    }
}
