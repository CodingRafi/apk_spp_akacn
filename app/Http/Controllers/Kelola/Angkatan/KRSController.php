<?php

namespace App\Http\Controllers\Kelola\Angkatan;

use App\Http\Controllers\Controller;
use App\Models\Kurikulum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KRSController extends Controller
{
    public function getSemester($prodi_id, $tahun_ajaran_id){
        $data = DB::table('semesters')
            ->select('tahun_semester.id', 'semesters.nama')
            ->join('tahun_semester', 'tahun_semester.semester_id', 'semesters.id')
            ->leftJoin('tahun_kurikulum', 'tahun_semester.id', 'tahun_kurikulum.tahun_semester_id')
            ->where('tahun_semester.prodi_id', $prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $tahun_ajaran_id)
            ->whereNull('tahun_kurikulum.tahun_semester_id')
            ->when(request('tahun_semester_id') && request('tahun_semester_id') != '', function($q){
                $q->orWhere('tahun_semester.id', request('tahun_semester_id'));
            })
            ->get();

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function dataKurikulum(){}

    public function create(){
        $kurikulum = Kurikulum::all();
        return view('data_master.prodi.angkatan.krs.form', compact('kurikulum'));
    }
}
