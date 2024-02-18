<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Http\Requests\MatkulRequest;
use App\Models\Matkul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MatkulController extends Controller
{
    public function data($kurikulum_id)
    {
        if ($kurikulum_id != ':id') {
            $datas = Matkul::where('kurikulum_id', $kurikulum_id)->get();
        } else {
            $datas = [];
        }

        foreach ($datas as $data) {
            $options = '';

            $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.mata-kuliah.show', $data->id) . "`, `Edit Mata Kuliah`, `#matkul`)' type='button'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.rombel.destroy', $data->id) . "`)' type='button'>
                                                Hapus
                                            </button>";
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('prodi', function ($datas) {
                return $datas->prodi->nama;
            })
            ->rawColumns(['options'])
            ->make(true);
    }

    public function store(MatkulRequest $request)
    {
        DB::beginTransaction();
        try {
            $requestParse = $request->all();
            $requestParse['id'] = generateUuid();
            $data = Matkul::create($requestParse);
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

    public function show($id)
    {
        $data = Matkul::find($id);
        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(MatkulRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $requestParse = $request->except('_method', '_token');
            $matkul = Matkul::find($id);
            $data = $matkul->update($requestParse);
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

    public function destroy(Matkul $matkul)
    {
        //
    }
}
