<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TranskipController extends Controller
{
    public function index()
    {
        return view('mahasiswa.transkip.index');
    }

    public function data()
    {
        $user = Auth::user();
        $data = DB::table('rekap_krs_matkul')
                ->select('rekap_krs_matkul.*', 'semesters.nama as semester', 'matkuls.kode as kode_mk', 'matkuls.nama as matkul')
                ->join('tahun_semester', 'rekap_krs_matkul.tahun_semester_id', '=', 'tahun_semester.id')
                ->join('semesters', 'tahun_semester.semester_id', '=', 'semesters.id')
                ->join('tahun_matkul', 'rekap_krs_matkul.tahun_matkul_id', '=', 'tahun_matkul.id')
                ->join('matkuls', 'tahun_matkul.matkul_id', '=', 'matkuls.id')
                ->where('mhs_id', $user->id)
                ->get()
                ->groupBy('semester');

        return response()->json([
            'data' => $data
        ], 200);
    }
}
