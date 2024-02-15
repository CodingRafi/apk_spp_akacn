<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\TahunAjaran;
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
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteData(`" . route('data-master.prodi.destroy', $data->id) . "`)'>
                                        Hapus
                                    </button>";
            }
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function create()
    {
        return view('data_master.prodi.form');
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required']);
        Prodi::create(['nama' => $request->nama]);
        return redirect()->route('data-master.prodi.index')->with('success', 'Berhasil ditambahkan');
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

    public function angkatanDetail($prodi_id, $tahun_ajaran_id){
        $prodi = Prodi::where('id', $prodi_id)->count();
        $tahun_ajaran = TahunAjaran::where('id', $tahun_ajaran_id)->count();

        if ($prodi < 1 || $tahun_ajaran < 1) {
            abort(404);
        }

        return view('data_master.prodi.angkatan.index', compact('prodi_id', 'tahun_ajaran_id'));
    }

    public function edit($id)
    {
        $data = Prodi::findOrFail($id);
        return view('data_master.prodi.form', compact('data'));
    }

    public function update(Request $request, Prodi $prodi)
    {
        $request->validate(['nama' => 'required']);
        $prodi->update(['nama' => $request->nama]);
        return redirect()->route('data-master.prodi.index')->with('success', 'Berhasil diubah');
    }

    public function destroy(Prodi $prodi)
    {
        DB::beginTransaction();
        try {
            $prodi->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal dihapus');
        }
    }
}
