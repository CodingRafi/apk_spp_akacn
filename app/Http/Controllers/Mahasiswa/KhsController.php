<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\User;
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

    private function validateMhsId($mhs_id = null)
    {
        $user = Auth::user();

        if ($user->hasRole('mahasiswa')) {
            $mhs_id = $user->id;
        }

        $user = User::findOrFail($mhs_id);

        if ((!$user->hasRole('mahasiswa') && $mhs_id == null) || $user->mahasiswa == null) {
            abort(404);
        }

        return $mhs_id;
    }

    public function dataSemester($mhs_id = null)
    {
        $mhs_id = $this->validateMhsId($mhs_id);
        $mhs = User::findOrFail($mhs_id)->mahasiswa;

        $datas = DB::table('tahun_semester')
            ->select('tahun_semester.id', 'semesters.nama')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.prodi_id', $mhs->prodi_id)
            ->where('tahun_semester.tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->orderBy('semesters.id', 'asc')
            ->get();

        foreach ($datas as $data) {
            $data->options = '<a href="' . route('khs.show', ['tahun_semester_id' => $data->id, 'mhs_id' => $mhs_id]) . '" class="btn btn-primary">Detail</a>';
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options'])
            ->make(true);
    }

    public function show($tahun_semester_id, $mhs_id = null)
    {
        $tahun_semester = DB::table('tahun_semester')
            ->select('tahun_semester.id', 'semesters.nama')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.id', $tahun_semester_id)
            ->first();

        $kuesioner = DB::table('kuesioners')
            ->where('status', '1')
            ->get();

        $mhs_id = $this->validateMhsId($mhs_id);

        return view('mahasiswa.khs.show', compact('tahun_semester', 'kuesioner', 'mhs_id'));
    }

    public function data($tahun_semester_id, $mhs_id = null)
    {
        $mhs_id = $this->validateMhsId($mhs_id);
        $user = User::findOrFail($mhs_id);
        $tahunSemester = DB::table('tahun_semester')
            ->select('id')
            ->where('id', '<=', $tahun_semester_id)
            ->where('prodi_id', $user->mahasiswa->prodi_id)
            ->where('tahun_ajaran_id', $user->mahasiswa->tahun_masuk_id)
            ->get()
            ->pluck('id')
            ->toArray();

        $ipk = DB::table('rekap_krs_matkul')
            ->select(DB::raw('SUM(bobot_x_sks) as bobot_x_sks'), DB::raw('SUM(jml_sks) as jml_sks'))
            ->whereIn('tahun_semester_id', $tahunSemester)
            ->where('mhs_id', $user->id)
            ->whereNotNull('kuesioner')
            ->where('status', '1')
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
            ->select(
                'users.name',
                'users.login_key as nim',
                'prodi.nama as prodi',
                'profile_mahasiswas.rombel_id',
                'profile_mahasiswas.tahun_masuk_id',
                'profile_mahasiswas.prodi_id'
            )
            ->join('profile_mahasiswas', 'profile_mahasiswas.user_id', 'users.id')
            ->join('prodi', 'prodi.id', 'profile_mahasiswas.prodi_id')
            ->where('users.id', Auth::user()->id)
            ->first();

        $getRombel = getRombelMhs(Auth::user()->id);

        $data->rombel = $getRombel['nama'];
        $data->dosenPa = $getRombel['dosen_pa'];

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

        $tahunSemesterIpk = DB::table('tahun_semester')
            ->select('id')
            ->where('id', '<=', $tahun_semester_id)
            ->where('prodi_id', Auth::user()->mahasiswa->prodi_id)
            ->where('tahun_ajaran_id', Auth::user()->mahasiswa->tahun_masuk_id)
            ->get()
            ->pluck('id')
            ->toArray();

        $ipk = DB::table('rekap_krs_matkul')
            ->select(DB::raw('SUM(bobot_x_sks) as bobot_x_sks'), DB::raw('SUM(jml_sks) as jml_sks'))
            ->whereIn('tahun_semester_id', $tahunSemesterIpk)
            ->where('mhs_id', Auth::user()->id)
            ->whereNotNull('kuesioner')
            ->where('status', '1')
            ->first();

        $admin = DB::table('users')
            ->where('id', '1')
            ->first();

        return Pdf::loadView('mahasiswa.khs.print', compact('data', 'khs', 'tahunSemester', 'admin', 'ipk'))
            ->stream('khs.pdf');
    }
}
