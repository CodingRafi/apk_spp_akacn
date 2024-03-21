<?php

namespace App\Http\Controllers;

use App\Models\JenisKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class JenisKelasController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_jenis_kelas', ['only' => ['index', 'store']]);
        $this->middleware('permission:add_jenis_kelas', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_jenis_kelas', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_jenis_kelas', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('data_master.jenis_kelas.index');
    }

    public function data()
    {
        $datas = JenisKelas::all();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_jenis_kelas')) {
                $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.jenis-kelas.show', $data->id) . "`, `Edit Jenis Kelas`, `#jenis_kelas`)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";
            }

            if (auth()->user()->can('delete_jenis_kelas')) {
                $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.jenis-kelas.destroy', $data->id) . "`)' type='button'>
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

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required'
        ]);

        JenisKelas::create([
            'nama' => $request->nama
        ]);

        return response()->json([
            'message' => 'Berhasil disimpan!'
        ], 200);
    }

    public function show($id)
    {
        $data = JenisKelas::findOrFail($id);
        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required'
        ]);

        JenisKelas::findOrFail($id)->update([
            'nama' => $request->nama
        ]);

        return response()->json([
            'message' => 'Berhasil diupdate!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\JenisKelas  $jenisKelas
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            JenisKelas::where('id', $id)->delete();
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
}
