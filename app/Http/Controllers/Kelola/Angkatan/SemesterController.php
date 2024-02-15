<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SemesterController extends Controller
{
    public function get($prodi_id, $tahun_ajaran_id)
    {
        $data = DB::table('semesters')
            ->select('semesters.*')
            ->leftJoin('tahun_semester', function ($join) use ($prodi_id) {
                $join->on('tahun_semester.semester_id', 'semesters.id')
                    ->where('tahun_semester.prodi_id', $prodi_id);
            })
            ->join('tahun_ajarans', 'tahun_ajarans.id', 'semesters.tahun_ajaran_id')
            ->whereNull('tahun_semester.semester_id')
            ->where('tahun_ajarans.id', ">=", $tahun_ajaran_id)
            ->where('semesters.status', 1)
            ->get();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function data($prodi_id, $tahun_ajaran_id)
    {
        $datas = DB::table('semesters')
            ->select('semesters.*')
            ->join('tahun_semester', 'tahun_semester.semester_id', 'semesters.id')
            ->where('tahun_semester.prodi_id', $prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $tahun_ajaran_id)
            ->get();

        foreach ($datas as $data) {
            $data->options = "<button class='btn btn-danger mx-2' onclick='deleteData(`" . route('data-master.prodi.semesters.destroy', $data->id) . "`)'>
                Hapus
            </button>";
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'prodi_id' => 'required',
            'tahun_ajaran_id' => 'required',
            'semester_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            DB::table('tahun_semester')->insert([
                'prodi_id' => $request->prodi_id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'semester_id' => $request->semester_id
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Berhasil ditambahkan'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
