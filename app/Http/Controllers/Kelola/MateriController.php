<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MateriController extends Controller
{
    public function index()
    {
        return view('data_master.kurikulum.materi.index');
    }

    public function data($matkul_id)
    {
        $datas = DB::table('matkul_materi')
            ->where('matkul_id', $matkul_id)
            ->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . " <button class='btn btn-warning'
                            onclick='editForm(`" . route('data-master.mata-kuliah.materi.show', ['matkul_id' => request('matkul_id'), 'materi_id' => $data->id]) . "`, `Edit Mater`, `#materi`)' type='button'>
                            <i class='ti-pencil'></i>
                            Edit
                        </button>";

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.mata-kuliah.materi.destroy', ['matkul_id' => request('matkul_id'), 'materi_id' => $data->id]) . "`)' type='button'>
                                                    Hapus
                                                </button>";
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function store(Request $request, $matkul_id)
    {
        $request->validate([
            'materi' => 'required',
            'type' => 'required'
        ]);

        DB::table('matkul_materi')->insert([
            'materi' => $request->materi,
            'type' => $request->type,
            'matkul_id' => $matkul_id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }

    public function show($matkul_id, $materi_id)
    {
        $data = DB::table('matkul_materi')
            ->where('id', $materi_id)
            ->first();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $matkul_id, $materi_id)
    {
        $request->validate([
            'materi' => 'required',
            'type' => 'required'
        ]);

        DB::table('matkul_materi')
            ->where('id', $materi_id)
            ->update([
                'materi' => $request->materi,
                'type' => $request->type,
            ]);

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }

    public function destroy($matkul_id, $materi_id)
    {
        DB::beginTransaction();
        try {
            DB::table('matkul_materi')
                ->where('id', $materi_id)
                ->delete();
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
