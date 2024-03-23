<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PembayaranSemesterController extends Controller
{
    public function getSemester($prodi_id, $tahun_ajaran_id)
    {
        $data = DB::table('semesters')
            ->select('tahun_semester.id', 'semesters.nama')
            ->join('tahun_semester', 'tahun_semester.semester_id', 'semesters.id')
            ->leftJoin('tahun_pembayaran', 'tahun_semester.id', 'tahun_pembayaran.tahun_semester_id')
            ->where('tahun_semester.prodi_id', $prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $tahun_ajaran_id)
            ->whereNull('tahun_pembayaran.tahun_semester_id')
            ->when(request('tahun_semester_id') && request('tahun_semester_id') != '', function($q){
                $q->orWhere('tahun_semester.id', request('tahun_semester_id'));
            })
            ->get();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function data($prodi_id, $tahun_ajaran_id)
    {
        $datas = DB::table('tahun_pembayaran')
            ->select('tahun_pembayaran.*', 'semesters.nama as semester')
            ->join('tahun_semester', 'tahun_semester.id', 'tahun_pembayaran.tahun_semester_id')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.prodi_id', $prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $tahun_ajaran_id)
            ->get();

        foreach ($datas as $data) {
            $options = '';

            $options = $options . " <button class='btn btn-warning'
                        onclick='editForm(`" . route('data-master.prodi.pembayaran.show', ['prodi_id' => $prodi_id, 'tahun_ajaran_id' => $tahun_ajaran_id, 'id' => $data->id]) . "`, `Edit Pembayaran`, `#PembayaranSemester`, getSemesterPembayaran)'>
                        <i class='ti-pencil'></i>
                        Edit
                    </button>";

            $options = $options . "<button class='btn btn-danger mx-2' onclick='deleteDataAjax(`" . route('data-master.prodi.pembayaran.destroy', ['prodi_id' => $prodi_id, 'tahun_ajaran_id' => $tahun_ajaran_id, 'id' => $data->id]) . "`, () => {tablePembayaranSemester.ajax.reload()})' type='button'>
                                                Hapus
                                            </button>";
            $data->options = $options;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editColumn('publish', function ($data) {
                return ($data->publish ? 'Ya' : 'Tidak');
            })
            ->editColumn('nominal', function ($datas) {
                return formatRupiah($datas->nominal);
            })
            ->rawColumns(['options'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'semester_id' => 'required',
            'nominal' => 'required',
            'ket' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('tahun_pembayaran')->insert([
                'tahun_semester_id' => $request->semester_id,
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

    public function show($prodi_id, $tahun_ajaran_id, $id){
        $data = DB::table('tahun_pembayaran')
                        ->select('tahun_pembayaran.*')
                        ->join('tahun_semester', 'tahun_semester.id', 'tahun_pembayaran.tahun_semester_id')
                        ->where('tahun_semester.prodi_id', $prodi_id)
                        ->where('tahun_semester.tahun_ajaran_id', $tahun_ajaran_id)
                        ->where('tahun_pembayaran.id', $id)
                        ->first();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $prodi_id, $tahun_ajaran_id, $id)
    {
        $request->validate([
            'nominal' => 'required',
            'ket' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('tahun_pembayaran')
                ->where('id', $id)
                ->update([
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

    public function destroy($prodi_id, $tahun_ajaran_id, $id){
        DB::beginTransaction();
        try {
            DB::table('tahun_pembayaran')
                ->where('id', $id)
                ->delete();
            DB::commit();
            return response()->json([
                'message' => 'Berhasil dihapus'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
