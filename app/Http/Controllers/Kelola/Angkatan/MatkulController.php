<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use App\Http\Requests\MatkulAngkatanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MatkulController extends Controller
{
    public function data($prodi_id, $tahun_ajaran_id)
    {
        $datas = DB::table('tahun_matkul')
            ->select('users.name as dosen', 'matkuls.nama as matkul', 'kurikulums.nama as kurikulum', 'matkuls.kode', 'tahun_matkul.id')
            ->join('users', 'users.id', 'tahun_matkul.dosen_id')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->join('kurikulums', 'kurikulums.id', 'matkuls.kurikulum_id')
            ->where('tahun_matkul.prodi_id', $prodi_id)
            ->where('tahun_matkul.tahun_ajaran_id', $tahun_ajaran_id)
            ->get();

        foreach ($datas as $data) {
            $options = '';

            if (auth()->user()->can('edit_matkul')) {
                $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.prodi.matkul.show', ['prodi_id' => $prodi_id, 'tahun_ajaran_id' => $tahun_ajaran_id, 'id' => $data->id]) . "`, `Edit Mata Kuliah`, `#Matkul`)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";
            }

            // if (auth()->user()->can('delete_matkul')) {
            //     $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.matkul.destroy', $data->id) . "`)' type='button'>
            //                             Hapus
            //                         </button>";
            // }

            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addColumn('rombel', function($datas){
                $rombel = DB::table('tahun_matkul_rombel')
                    ->select('rombels.nama as rombel')
                    ->join('rombels', 'rombels.id', 'tahun_matkul_rombel.rombel_id')
                    ->where('tahun_matkul_rombel.tahun_matkul_id', $datas->id)
                    ->get()
                    ->pluck('rombel');
                return implode(', ', $rombel->toArray());
            })
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function store(MatkulAngkatanRequest $request, $prodi_id, $tahun_ajaran_id)
    {
        DB::beginTransaction();
        try {
            $requestParse = $request->except('_method', '_token', 'rombel_id', 'ruang_id');
            $requestParse['prodi_id'] = $prodi_id;
            $requestParse['tahun_ajaran_id'] = $tahun_ajaran_id;
            DB::table('tahun_matkul')->insert($requestParse);
            $tahun_matkul_id = DB::getPdo()->lastInsertId();

            foreach ($request->rombel_id as $rombel_id) {
                DB::table('tahun_matkul_rombel')->insert([
                    'tahun_matkul_id' => $tahun_matkul_id,
                    'rombel_id' => $rombel_id
                ]);
            }

            foreach ($request->ruang_id as $ruang_id) {
                DB::table('tahun_matkul_ruang')->insert([
                    'tahun_matkul_id' => $tahun_matkul_id,
                    'ruang_id' => $ruang_id
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Berhasil ditambah'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function show($prodi_id, $tahun_ajaran_id, $id)
    {
        $data = DB::table('tahun_matkul')
            ->where('tahun_matkul.prodi_id', $prodi_id)
            ->where('tahun_matkul.tahun_ajaran_id', $tahun_ajaran_id)
            ->where('tahun_matkul.id', $id)
            ->first();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(MatkulAngkatanRequest $request, $prodi_id, $tahun_ajaran_id, $id)
    {
        DB::beginTransaction();
        try {
            $requestParse = $request->except('_method', '_token');
            DB::table('tahun_matkul')
                ->where('id', $id)
                ->where('prodi_id', $prodi_id)
                ->where('tahun_ajaran_id', $tahun_ajaran_id)
                ->update($requestParse);
            DB::commit();
            return response()->json([
                'message' => 'Berhasil diubah'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
