<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Kurikulum;
use App\Models\Prodi;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KurikulumController extends Controller
{
    public function index()
    {
        return view('data_master.kurikulum.index');
    }

    public function create()
    {
        $prodis = Prodi::all();
        $tahunSemesters = Semester::all();
        return view('data_master.kurikulum.form', compact('prodis', 'tahunSemesters'));
    }

    public function data(){
        $datas = Kurikulum::all();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . "<a href='" . route('data-master.kurikulum.edit', $data->id) . "' class='btn btn-warning mx-2'>Edit</a>";

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.kurikulum.destroy', $data->id) . "`)' type='button'>
                                                Hapus
                                            </button>";
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('jml_matkul', function($datas){
                return DB::table('matkuls')->where('kurikulum_id', $datas->id)->count();
            })
            ->rawColumns(['options'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'jml_sks_lulus' => 'required',
            'jml_sks_wajib' => 'required',
            'jml_sks_pilihan' => 'required',
            'tahun_semester_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = Kurikulum::create([
                'id' => generateUuid(),
                'nama' => $request->nama,
                'jml_sks_lulus' => $request->jml_sks_lulus,
                'jml_sks_wajib' => $request->jml_sks_wajib,
                'jml_sks_pilihan' => $request->jml_sks_pilihan,
                'tahun_semester_id' => $request->tahun_semester_id
            ]);
            DB::commit();
            return response()->json([
                'data' => $data,
                'message' => 'Berhasil ditambahkan'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function edit(Kurikulum $kurikulum)
    {
        $prodis = Prodi::all();
        return view('data_master.kurikulum.form', [
            'prodis' => $prodis,
            'data' => $kurikulum
        ]);
    }

    public function update(Request $request, Kurikulum $kurikulum)
    {
        $request->validate([
            'nama' => 'required',
            'jml_sks_lulus' => 'required',
            'jml_sks_wajib' => 'required',
            'jml_sks_pilihan' => 'required',
            'tahun_semester_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $kurikulum->update([
                'nama' => $request->nama,
                'jml_sks_lulus' => $request->jml_sks_lulus,
                'jml_sks_wajib' => $request->jml_sks_wajib,
                'jml_sks_pilihan' => $request->jml_sks_pilihan,
                'tahun_semester_id' => $request->tahun_semester_id
            ]);
            DB::commit();
            return response()->json([
                'data' => $kurikulum,
                'message' => 'Berhasil diupdate'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Kurikulum $kurikulum)
    {
        DB::beginTransaction();
        try {
            $kurikulum->delete();
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
