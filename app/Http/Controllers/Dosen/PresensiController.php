<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    public function index(){
        $semesterAktif = DB::table('semesters')->where('status', "1")->orderBy('id', 'desc')->first();

        //? Untuk get siapa aja yang menggunakan semester ini
        $tahun_semester = DB::table('tahun_semester')->where('semester_id', $semesterAktif->id)->get();
        dd($tahun_semester);

        return view('dosen.presensi.index');
    }
}
