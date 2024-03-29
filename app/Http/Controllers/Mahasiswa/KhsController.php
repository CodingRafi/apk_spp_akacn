<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KhsController extends Controller
{
    public function index()
    {
        return view('mahasiswa.khs.index');
    }

    public function dataSemester()
    {
        $user = Auth::user();
        $mhs = $user->mahasiswa;

        $datas = DB::table('tahun_semester')
            ->select('tahun_semester.id', 'semesters.nama')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.prodi_id', $mhs->prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->get();

        foreach ($datas as $data) {
            $data->options = '<a href="' . route('khs.show', $data->id) . '" class="btn btn-primary">Detail</a>';
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function show($tahun_semester_id)
    {
        $tahun_semester = DB::table('tahun_semester')
            ->select('tahun_semester.id', 'semesters.nama')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.id', $tahun_semester_id)
            ->first();

        $kuesioner = DB::table('kuesioners')
            ->where('status', '1')
            ->get();

        return view('mahasiswa.khs.show', compact('tahun_semester', 'kuesioner'));
    }

    public function data($tahun_semester_id)
    {
        $user = Auth::user();
        $tahunSemester = DB::table('tahun_semester')
            ->select('id')
            ->where('id', '<=', $tahun_semester_id)
            ->where('prodi_id', $user->mahasiswa->prodi_id)
            ->where('tahun_ajaran_id', $user->mahasiswa->tahun_masuk_id)
            ->get()
            ->pluck('id')
            ->toArray();

        $ipk = DB::table('rekap_krs_matkul')
            ->select(DB::raw('SUM("bobot_x_sks") as bobot_x_sks'), DB::raw('SUM("jml_sks") as jml_sks'))
            ->whereIn('tahun_semester_id', $tahunSemester)
            ->first();

        $khs = DB::table('rekap_krs_matkul as a')
            ->select('a.*', 'matkuls.nama as matkul', 'matkuls.kode as kode_mk')
            ->join('tahun_matkul', 'tahun_matkul.id', 'a.tahun_matkul_id')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->where('a.tahun_semester_id', $tahun_semester_id)
            ->where('a.mhs_id', $user->id)
            ->get();

        return response()->json([
            'data' => $khs,
            'ipk' => $ipk
        ], 200);
    }

    public function print($tahun_semester_id)
    {
        $data = DB::table('users')
            ->select('users.name', 'users.login_key as nim', 'dosen.name as dosenPa', 'prodi.nama as prodi')
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
            ->join('rombels', 'rombels.id', 'profile_mahasiswas.rombel_id')
            ->join('rombel_tahun_ajarans', function ($q) {
                $q->on('rombel_tahun_ajarans.rombel_id', 'rombels.id')
                    ->on('rombel_tahun_ajarans.tahun_masuk_id', 'profile_mahasiswas.tahun_masuk_id');
            })
            ->join('users as dosen', 'dosen.id', 'rombel_tahun_ajarans.dosen_pa_id')
            ->join('prodi', 'prodi.id', 'profile_mahasiswas.prodi_id')
            ->where('users.id', Auth::user()->id)
            ->first();

        $khs = DB::table('rekap_krs_matkul as a')
            ->select('a.*', 'matkuls.nama as matkul', 'matkuls.kode as kode_mk')
            ->join('tahun_matkul', 'tahun_matkul.id', 'a.tahun_matkul_id')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->where('a.tahun_semester_id', $tahun_semester_id)
            ->where('a.mhs_id', Auth::user()->id)
            ->get();

        $tahunSemester = DB::table('tahun_semester')
            ->select('semesters.nama')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.id', $tahun_semester_id)
            ->first();

        $admin = DB::table('users')
            ->where('id', '1')
            ->first();

        return Pdf::loadView('mahasiswa.khs.print', compact('data', 'khs', 'tahunSemester', 'admin'))
            ->stream('khs.pdf');
    }
}
