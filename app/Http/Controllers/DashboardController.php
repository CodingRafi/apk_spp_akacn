<?php

namespace App\Http\Controllers;

use App\Models\KalenderAkademik;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    private function validateRole($role)
    {
        $getRole = getRole()->name;

        if ($getRole != $role) {
            abort(404);
        }
    }

    public function admin()
    {
        $this->validateRole('admin');

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
                ->select('krs.status as name', DB::raw('count(*) as y'))
                ->when(request('matkul'), function ($q) {
                    $q->join('krs_matkul', 'krs_matkul.krs_id', 'krs.id')
                        ->where('krs_matkul.tahun_matkul_id', request('matkul'));
                })
                ->where('krs.tahun_semester_id', request('semester'))
                ->groupBy('krs.status')
                ->get()
                ->toArray();

            $presensi = DB::table('jadwal')
                ->select(DB::raw('count(*) as y'), 'jadwal_presensi.status as name')
                ->join('jadwal_presensi', 'jadwal_presensi.jadwal_id', 'jadwal.id')
                ->join('users', 'users.id', 'jadwal_presensi.mhs_id')
                ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
                ->when(request('matkul'), function ($q) {
                    $q->where('jadwal.tahun_matkul_id', request('matkul'));
                })
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
                ->when(request('matkul'), function ($q) {
                    $q->where('mhs_nilai.tahun_matkul_id', request('matkul'));
                })
                ->where('mhs_nilai.tahun_semester_id', request('semester'))
                ->where('profile_mahasiswas.prodi_id', request('prodi'))
                ->where('profile_mahasiswas.tahun_masuk_id', request('tahun_ajaran'))
                ->groupBy('mutu.nama')
                ->get();
        }

        $kalenderAkademik = KalenderAkademik::select('start_time as start', 'finish_time as end', 'comments as title')->get();

        return view('dashboard.admin', compact(
            'users',
            'tahunAjaran',
            'prodis',
            'pembayaran',
            'krs',
            'presensi',
            'nilai',
            'kalenderAkademik'
        ));
    }

    public function asisten()
    {
        $this->validateRole('asisten');
        $rekap = DB::table('jadwal')
            ->select('approved', DB::raw('count(*) as total'))
            ->where('pengajar_id', auth()->user()->id)
            ->groupBy('approved')
            ->pluck('total', 'approved');

        $totalMengajar = $rekap->sum();
        $totalMengajarMenunggu = $rekap[1] ?? 0;
        $totalMengajarDisetujui = $rekap[2] ?? 0;
        $totalMengajarDitolak = $rekap[3] ?? 0;

        $kalenderAkademik = KalenderAkademik::select('start_time as start', 'finish_time as end', 'comments as title')->get();

        return view('dashboard.asisten', compact('totalMengajar', 'totalMengajarMenunggu', 'totalMengajarDisetujui', 'totalMengajarDitolak', 'kalenderAkademik'));
    }

    public function dosen()
    {
        $this->validateRole('dosen');
        $rekap = DB::table('jadwal')
            ->select('approved', DB::raw('count(*) as total'))
            ->where('pengajar_id', auth()->user()->id)
            ->groupBy('approved')
            ->pluck('total', 'approved');

        $totalMengajar = $rekap->sum();
        $totalMengajarMenunggu = $rekap[1] ?? 0;
        $totalMengajarDisetujui = $rekap[2] ?? 0;
        $totalMengajarDitolak = $rekap[3] ?? 0;

        $kalenderAkademik = KalenderAkademik::select('start_time as start', 'finish_time as end', 'comments as title')->get();

        return view('dashboard.dosen', compact('totalMengajar', 'totalMengajarMenunggu', 'totalMengajarDisetujui', 'totalMengajarDitolak', 'kalenderAkademik'));
    }

    public function petugas()
    {
        $this->validateRole('petugas');
        $totalVerifikasi = DB::table('pembayarans')
            ->where('verify_id', auth()->user()->id)
            ->count();

        $kalenderAkademik = KalenderAkademik::select('start_time as start', 'finish_time as end', 'comments as title')->get();

        return view('dashboard.petugas', compact('totalVerifikasi', 'kalenderAkademik'));
    }

    public function mahasiswa()
    {
        $this->validateRole('mahasiswa');

        $tagihan = DB::table('rekap_pembayaran')
            ->select(DB::raw('SUM(sisa) as tagihan'))
            ->where('user_id', auth()->user()->id)
            ->first();

        $krs = DB::table('krs')
            ->select('krs.jml_sks_diambil', 'semesters.nama as semester')
            ->join('tahun_semester', 'krs.tahun_semester_id', 'tahun_semester.id')
            ->join('semesters', 'tahun_semester.semester_id', 'semesters.id')
            ->where('krs.status', 'diterima')
            ->where('krs.mhs_id', auth()->user()->id)
            ->orderBy('semesters.id', 'asc')
            ->get();

        $kalenderAkademik = KalenderAkademik::select('start_time as start', 'finish_time as end', 'comments as title')->get();

        return view('dashboard.mahasiswa', compact('tagihan', 'krs', 'kalenderAkademik'));
    }

    public function adminGetMatkul()
    {
        $data = DB::table('tahun_matkul')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->where('tahun_matkul.prodi_id', request('prodi'))
            ->where('tahun_matkul.tahun_ajaran_id', request('tahun_ajaran'))
            ->get();

        return response()->json($data, 200);
    }
}
