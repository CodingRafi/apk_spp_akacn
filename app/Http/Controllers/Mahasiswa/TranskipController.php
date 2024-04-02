<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
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
            ->select(
                'rekap_krs_matkul.*',
                'semesters.nama as semester',
                'matkuls.kode as kode_mk',
                'matkuls.nama as matkul'
            )
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

    public function print()
    {
        $user = Auth::user();
        $rekap = DB::table('rekap_krs_matkul')
            ->select(
                'rekap_krs_matkul.*',
                'semesters.nama as semester',
                'matkuls.kode as kode_mk',
                'matkuls.nama as matkul'
            )
            ->join('tahun_semester', 'rekap_krs_matkul.tahun_semester_id', '=', 'tahun_semester.id')
            ->join('semesters', 'tahun_semester.semester_id', '=', 'semesters.id')
            ->join('tahun_matkul', 'rekap_krs_matkul.tahun_matkul_id', '=', 'tahun_matkul.id')
            ->join('matkuls', 'tahun_matkul.matkul_id', '=', 'matkuls.id')
            ->where('mhs_id', $user->id)
            ->get()
            ->groupBy('semester');

        $data = DB::table('users')
            ->select(
                'users.name',
                'users.login_key as nim',
                'dosen.name as dosenPa',
                'prodi.nama as prodi',
                'profile_mahasiswas.tahun_masuk_id as angkatan'
            )
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
            ->join('rombels', 'rombels.id', 'profile_mahasiswas.rombel_id')
            ->join('rombel_tahun_ajarans', function ($q) {
                $q->on('rombel_tahun_ajarans.rombel_id', 'rombels.id')
                    ->on('rombel_tahun_ajarans.tahun_masuk_id', 'profile_mahasiswas.tahun_masuk_id');
            })
            ->join('users as dosen', 'dosen.id', 'rombel_tahun_ajarans.dosen_pa_id')
            ->join('prodi', 'prodi.id', 'profile_mahasiswas.prodi_id')
            ->where('users.id', $user->id)
            ->first();

        return Pdf::loadView('mahasiswa.transkip.print', compact('data', 'rekap'))
            ->stream('transkip.pdf');
    }
}
