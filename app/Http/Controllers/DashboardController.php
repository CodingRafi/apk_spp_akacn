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
        }


        return view('dashboard.admin', compact(
            'users',
            'tahunAjaran',
            'prodis',
            'pembayaran',
            'krs'
        ));
    }

    public function asdos(){
        return view('dashboard.asdos');
    }

    public function dosen(){
        return view('dashboard.dosen');
    }

    public function petugas(){
        return view('dashboard.petugas');
    }

    public function mahasiswa(){
        return view('dashboard.mahasiswa');
    }
}
