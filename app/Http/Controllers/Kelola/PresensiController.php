<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PresensiController extends Controller
{
    public function index()
    {
        return view('kelola.presensi.index');
    }

    public function getTahunAjaran()
    {
        $datas = TahunAjaran::all();

        foreach ($datas as $data) {
            $data->options = "<a href='" . route('kelola-presensi.presensi.show', ['tahun_ajaran_id' => $data->id]) . "' class='btn btn-info mx-2'>Detail</a>";
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->rawColumns(['options', 'status'])
            ->make(true);
    }

    public function show($tahun_ajaran_id)
    {
        $tahunSemester = DB::table('tahun_semester')
            ->select('tahun_semester.*', 'semesters.nama')
            ->join('semesters', 'tahun_semester.semester_id', '=', 'semesters.id')
            ->where('tahun_semester.tahun_ajaran_id', $tahun_ajaran_id)
            ->get()
            ->pluck('nama', 'id');
        return view('kelola.presensi.showTahunAjaran', compact('tahun_ajaran_id', 'tahunSemester'));
    }

    public function getJadwal($tahun_ajaran_id)
    {
        $tahunSemester = DB::table('tahun_semester')
            ->where('tahun_ajaran_id', $tahun_ajaran_id)
            ->first();

        if ($tahunSemester) {
            $jadwals = DB::table('jadwal')
                ->select('jadwal.*', 'matkuls.nama as matkul', 'matkuls.kode as kode_matkul')
                ->join('tahun_matkul', 'jadwal.tahun_matkul_id', '=', 'tahun_matkul.id')
                ->join('matkuls', 'tahun_matkul.matkul_id', '=', 'matkuls.id')
                ->where('tahun_semester_id', $tahunSemester->id)
                ->get();
        }

        $datas = isset($jadwals) ? $jadwals : [];

        foreach ($datas as $data) {
            $data->options = "<a href='" . route('kelola-presensi.presensi.showJadwal', ['tahun_ajaran_id' => $tahun_ajaran_id, 'jadwal_id' => $data->id]) . "' class='btn btn-info mx-2'>Detail</a>";;
        }

        return DataTables::of($datas)
            ->addIndexColumn()
            ->editColumn('tgl', function ($datas) {
                return parseDate($datas->tgl);
            })
            ->rawColumns(['options', 'status'])
            ->make(true);
    }

    public function showJadwal($tahun_ajaran_id, $jadwal_id)
    {
        $data = DB::table('jadwal')
            ->select('jadwal.*', 'matkuls.nama as matkul', 'users.name as pengajar')
            ->join('tahun_matkul', 'jadwal.tahun_matkul_id', '=', 'tahun_matkul.id')
            ->join('matkuls', 'tahun_matkul.matkul_id', '=', 'matkuls.id')
            ->join('users', 'jadwal.pengajar_id', '=', 'users.id')
            ->where('jadwal.id', $jadwal_id)
            ->first();

        if (!$data) {
            abort(404);
        }

        $rombel = DB::table('tahun_matkul_rombel')
            ->select('rombels.id', 'rombels.nama')
            ->join('rombels', 'rombels.id', '=', 'tahun_matkul_rombel.rombel_id')
            ->where('tahun_matkul_rombel.tahun_matkul_id', $data->tahun_matkul_id)
            ->get();

        return view('kelola.presensi.showJadwal', compact('data', 'rombel'));
    }

    public function getPresensi($tahun_ajaran_id, $jadwal_id, $rombel_id)
    {
        $presensi = DB::table('users')
            ->select('users.id', 'users.name', 'users.login_key', 'jadwal_presensi.status', 'profile_mahasiswas.rombel_id')
            ->join('profile_mahasiswas', 'users.id', '=', 'profile_mahasiswas.user_id')
            ->leftJoin('jadwal_presensi', function ($join) use ($jadwal_id) {
                $join->on('jadwal_presensi.mhs_id', 'users.id')
                    ->where('jadwal_presensi.jadwal_id', $jadwal_id);
            })
            ->where('profile_mahasiswas.rombel_id', $rombel_id)
            ->where('profile_mahasiswas.tahun_masuk_id', $tahun_ajaran_id)
            ->get();

        return response()->json([
            'data' => $presensi
        ], 200);
    }

    public function getPresensiMhs($tahun_ajaran_id, $jadwal_id, $rombel_id, $mhs_id)
    {
        $presensi = DB::table('users')
            ->select('users.id', 'users.name', 'users.login_key', 'jadwal_presensi.status')
            ->join('profile_mahasiswas', 'users.id', '=', 'profile_mahasiswas.user_id')
            ->leftJoin('jadwal_presensi', function ($join) use ($jadwal_id) {
                $join->on('jadwal_presensi.mhs_id', 'users.id')
                    ->where('jadwal_presensi.jadwal_id', $jadwal_id);
            })
            ->where('profile_mahasiswas.rombel_id', $rombel_id)
            ->where('profile_mahasiswas.tahun_masuk_id', $tahun_ajaran_id)
            ->where('users.id', $mhs_id)
            ->first();

        return response()->json([
            'data' => $presensi
        ], 200);
    }

    public function updatePresensiMhs(Request $request, $tahun_ajaran_id, $jadwal_id, $rombel_id, $mhs_id)
    {
        $request->validate([
            'status' => 'required'
        ]);

        $data = [
            'jadwal_id' => $jadwal_id,
            'mhs_id' => $mhs_id,
            'status' => $request->status
        ];

        DB::table('jadwal_presensi')->updateOrInsert(
            ['jadwal_id' => $jadwal_id, 'mhs_id' => $mhs_id],
            $data
        );

        return response()->json([
            'message' => 'Berhasil disimpan'
        ], 200);
    }
}
