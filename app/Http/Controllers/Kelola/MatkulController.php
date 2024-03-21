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

            $options .= "<a href='" . route('data-master.mata-kuliah.materi.index', $data->id) . "' class='btn btn-info mx-2'>Materi</a>";

            $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.mata-kuliah.show', $data->id) . "`, `Edit Mata Kuliah`, `#matkul`)' type='button'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.mata-kuliah.destroy', $data->id) . "`, () => {tableMatkul.ajax.reload()})' type='button'>
                                                Hapus
                                            </button>";
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn('prodi', function ($datas) {
                $prodi = implode(', ', $datas->prodi->pluck('nama')->toArray());
                return $prodi;
            })
            ->rawColumns(['options'])
            ->make(true);
    }

    public function store(MatkulRequest $request)
    {
        DB::beginTransaction();
        try {
            $requestParse = $request->except('prodi_id', '_method', '_token');
            $requestParse['id'] = generateUuid();
            $data = Matkul::create($requestParse);

            $data->prodi()->sync($request->prodi_id);

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
        $data['prodi_id'] = $data->prodi->pluck('id');
        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(MatkulRequest $request, $id)
    {
        $matkul = Matkul::find($id);
        //? Validasi Prodi
        $oldProdi = $matkul->prodi->pluck('id')->toArray();
        $cek = array_diff($oldProdi, $request->prodi_id);

        if (count($cek) > 0) {
            foreach ($cek as $item) {
                $cekUseMatkul = DB::table('tahun_matkul')->where('matkul_id', $item)->count();
                if ($cekUseMatkul > 0) {
                    return response()->json([
                        'message' => 'Tidak bisa mengubah prodi, karena sudah ada yang menggunakan'
                    ], 200);
                }
            }
        }

        DB::beginTransaction();
        try {
            $requestParse = $request->except('_method', '_token', 'prodi_id');
            $data = $matkul->update($requestParse);
            $matkul->prodi()->sync($request->prodi_id);
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

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            DB::table('matkuls')
                ->where('id', $id)
                ->delete();
            DB::commit();
            return response()->json([
                'message' => 'Berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
