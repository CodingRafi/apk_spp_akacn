<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PotonganController extends Controller
{
    public function data($prodi_id, $tahun_ajaran_id)
    {
        $datas = DB::table('potongan_tahun_ajaran')
            ->select('potongan_tahun_ajaran.*', 'potongans.nama as potongan', 'semesters.nama as semester')
            ->join('potongans', 'potongans.id', 'potongan_tahun_ajaran.potongan_id')
            ->join('tahun_semester', 'tahun_semester.id', 'potongan_tahun_ajaran.tahun_semester_id')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.prodi_id', $prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $tahun_ajaran_id)
            ->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.prodi.potongan.show', ['prodi_id' => $prodi_id, 'tahun_ajaran_id' => $tahun_ajaran_id, 'id' => $data->id]) . "`, `Edit Potongan`, `#Potongan`)'>
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
            ->editColumn('publish', function ($data) {
                return $data->publish ? 'Ya' : 'Tidak';
            })
            ->editColumn('nominal', function ($datas) {
                return formatRupiah($datas->nominal);
            })
            ->rawColumns(['options'])
            ->make(true);
    }

    public function store(Request $request, $prodi_id, $tahun_ajaran_id)
    {
        $request->validate([
            'potongan_id' => 'required',
            'tahun_semester_id' => 'required',
            'nominal' => 'required',
            'ket' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('potongan_tahun_ajaran')->insert([
                'potongan_id' => $request->potongan_id,
                'tahun_semester_id' => $request->tahun_semester_id,
                'nominal' => $request->nominal,
                'ket' => $request->ket,
                'publish' => $request->publish ?? '0',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Berhasil ditambahkan'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function show($prodi_id, $tahun_ajaran_id, $id)
    {
        $data = DB::table('potongan_tahun_ajaran')
            ->where('id', $id)
            ->first();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $prodi_id, $tahun_ajaran_id, $id)
    {
        $request->validate([
            'potongan_id' => 'required',
            'tahun_semester_id' => 'required',
            'nominal' => 'required',
            'ket' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('potongan_tahun_ajaran')
                ->where('id', $id)
                ->update([
                    'potongan_id' => $request->potongan_id,
                    'tahun_semester_id' => $request->tahun_semester_id,
                    'nominal' => $request->nominal,
                    'ket' => $request->ket,
                    'publish' => $request->publish ?? '0',
                    'updated_at' => now()
                ]);

            DB::commit();
            return response()->json([
                'message' => 'Berhasil diubah'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
