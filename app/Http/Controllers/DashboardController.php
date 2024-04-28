<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function admin()
    {
        $users = Role::withCount('users')
            ->where('roles.name', '!=', 'admin')
            ->get();

        $prodis = DB::table('prodi')->get();
        $tahunAjaran = DB::table('tahun_ajarans')->get();
        $pembayaran = [];
        $semester = [];
        $krs = [];
        $presensi = collect([]);
        $nilai = collect([]);

        if (request('prodi') && request('tahun_ajaran') && request('semester')) {
            $semester = DB::table('tahun_semester')
                ->select('tahun_semester.tgl_mulai', 'tahun_semester.tgl_akhir')
                ->where('tahun_semester.id', request('semester'))
                ->first();

            $pembayaran = DB::table('pembayarans')
                ->select('status as name', DB::raw('count(*) as y'))
                ->whereBetween('created_at', [$semester->tgl_mulai, $semester->tgl_akhir])
                ->groupBy('status')
                ->get()
                ->toArray();

            $krs = DB::table('krs')
                ->select('status as name', DB::raw('count(*) as y'))
                ->where('tahun_semester_id', request('semester'))
                ->groupBy('status')
                ->get()
                ->toArray();

            $presensi = DB::table('jadwal')
                ->select(DB::raw('count(*) as y'), 'jadwal_presensi.status as name')
                ->join('jadwal_presensi', 'jadwal_presensi.jadwal_id', 'jadwal.id')
                ->join('users', 'users.id', 'jadwal_presensi.mhs_id')
                ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
                ->where('jadwal.tahun_semester_id', request('semester'))
                ->where('profile_mahasiswas.prodi_id', request('prodi'))
                ->where('profile_mahasiswas.tahun_masuk_id', request('tahun_ajaran'))
                ->groupBy('jadwal_presensi.status')
                ->get();

            $nilai = DB::table('mhs_nilai')
                ->select(DB::raw('count(mhs_nilai.id) as y'), 'mutu.nama')
                ->join('users', 'users.id', 'mhs_nilai.mhs_id')
                ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
                ->join('mutu', 'mutu.id', 'mhs_nilai.mutu_id')
                ->where('mhs_nilai.tahun_semester_id', request('semester'))
                ->where('profile_mahasiswas.prodi_id', request('prodi'))
                ->where('profile_mahasiswas.tahun_masuk_id', request('tahun_ajaran'))
                ->groupBy('mutu.nama')
                ->get();
        }

        return view('dashboard.admin', compact(
            'users',
            'tahunAjaran',
            'prodis',
            'pembayaran',
            'krs',
            'presensi',
            'nilai'
        ));
    }

    public function asdos()
    {
        return view('dashboard.asdos');
    }

    public function dosen()
    {
        return view('dashboard.dosen');
    }

    public function petugas()
    {
        return view('dashboard.petugas');
    }

    public function mahasiswa()
    {
        return view('dashboard.mahasiswa');
    }
}
