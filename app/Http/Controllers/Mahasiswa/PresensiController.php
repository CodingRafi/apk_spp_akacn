<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mhs = $user->mahasiswa;
        $tahun_semester = DB::table('tahun_semester')
            ->select('tahun_semester.id', 'semesters.nama')
            ->join('semesters', 'semesters.id', 'tahun_semester.semester_id')
            ->where('tahun_semester.tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->where('tahun_semester.prodi_id', $mhs->prodi_id)
            ->get();

        return view('mahasiswa.presensi.index', compact('tahun_semester'));
    }

    public function data()
    {
        $user = Auth::user();
        $tahunSemesterId = request('tahun_semester_id');

        $krs = DB::table('krs')
            ->select('id')
            ->where('mhs_id', $user->id)
            ->where('tahun_semester_id', $tahunSemesterId)
            ->where('status', 'diterima')
            ->first();

        if (!$krs) {
            return response()->json([
                'message' => 'Tidak ada KRS ditemukan'
            ], 200);
        }

        $krsMatkul = DB::table('krs_matkul')
            ->select('krs_matkul.tahun_matkul_id', 'matkuls.nama')
            ->join('tahun_matkul', 'tahun_matkul.id', 'krs_matkul.tahun_matkul_id')
            ->join('matkuls', 'matkuls.id', 'tahun_matkul.matkul_id')
            ->where('krs_matkul.krs_id', $krs->id)
            ->get();

        $tahunMatkulId = $krsMatkul->pluck('tahun_matkul_id');
        $jadwal = DB::table('jadwal')
            ->select('jadwal.id as jadwal_id', 'jadwal_presensi.status', 'jadwal.jenis_ujian', 'jadwal.type', 'jadwal.tahun_matkul_id')
            ->leftJoin('jadwal_presensi', function ($join) use ($user) {
                $join->on('jadwal.id', 'jadwal_presensi.jadwal_id')
                    ->where('jadwal_presensi.mhs_id', $user->id);
            })
            ->where('jadwal.tahun_semester_id', $tahunSemesterId)
            ->whereIn('jadwal.tahun_matkul_id', $tahunMatkulId)
            ->orderBy('jadwal.tgl', 'asc')
            ->get();

        $data = [];

        foreach ($krsMatkul as $matkul) {
            $getPresensi = $jadwal->filter(function ($row) use ($matkul) {
                return $row->tahun_matkul_id == $matkul->tahun_matkul_id;
            });

            $presensiPertemuan = $getPresensi->filter(function ($data) {
                return $data->type == 'pertemuan';
            });

            $presensi = [];

            for ($i = 0; $i <= config('services.max_pertemuan'); $i++) {
                $presensi[$i] = [
                    'jadwal_id' => $presensiPertemuan->get($i)->jadwal_id ?? null,
                    'status' => $presensiPertemuan->get($i)->status ?? null
                ];
            }

            $presensiUjian = $getPresensi->filter(function ($data) {
                return $data->type == 'ujian';
            });

            $resPresensi = [];

            foreach (config('services.ujian') as $key => $jenis) {
                $presensiCheck = $presensiUjian->firstWhere('jenis_ujian', $jenis['key']);
                $sliceData = array_slice($presensi, $jenis['indexStart'], (7 * ($key + 1)));
                $sliceData[] = [
                    'jadwal_id' => $presensiCheck ? $presensiCheck->jadwal_id : null,
                    'jadwal_id' => $presensiCheck ? $presensiCheck->jadwal_id : null,
                    'status' => $presensiCheck ? $presensiCheck->status : null,
                    'jenis' => $jenis['key']
                ];
                $resPresensi = array_merge($resPresensi, $sliceData);
            }

            $data[$matkul->tahun_matkul_id] = [
                'matkul' => $matkul->nama,
                'presensi' => $resPresensi
            ];
        }

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required'
        ]);

        $data = DB::table('jadwal')
            ->select('jadwal.*', 'tahun_matkul.jam_mulai', 'tahun_matkul.jam_akhir', 'tahun_matkul.cek_ip')
            ->join('tahun_matkul', 'tahun_matkul.id', '=', 'jadwal.tahun_matkul_id')
            ->where('kode', $request->kode)
            ->first();

        if (!$data) {
            return response()->json([
                'message' => 'Kode jadwal tidak ditemukan'
            ], 400);
        }

        //? Validasi IP
        if ($data->cek_ip == '1') {
            $whitelist_ip = DB::table('whitelist_ip')->get()->pluck('ip')->toArray();
            if (!in_array($request->ip(), $whitelist_ip)) {
                return response()->json([
                    'message' => 'Jaringan anda tidak valid!'
                ], 400);
            }
        }

        if (!$data->presensi_mulai) {
            return response()->json([
                'message' => 'Pengajar belum memulai pelajaran'
            ], 400);
        }

        $user = Auth::user();
        $mhs = $user->mahasiswa;

        //? Validasi tahun semester
        $tahunSemesterAktif = DB::table('tahun_semester')
            ->where('prodi_id', $mhs->prodi_id)
            ->where('tahun_ajaran_id', $mhs->tahun_masuk_id)
            ->where('status', '1')
            ->orderBy('id', 'desc')
            ->first();

        if ($tahunSemesterAktif->id != $data->tahun_semester_id) {
            return response()->json([
                'message' => 'Kode tidak valid!'
            ], 400);
        }

        //? validasi KRS
        $krs = DB::table('krs')
            ->where('mhs_id', Auth::user()->id)
            ->where('tahun_semester_id', $tahunSemesterAktif->id)
            ->first();

        if (!$krs || ($krs && $krs == 'pending')) {
            return response()->json([
                'message' => 'Anda harus mengambil KRS terlebih dahulu'
            ], 400);
        }
        //? Validasi KRS matkul
        $krsMatkul = DB::table('krs_matkul')
            ->where('krs_id', $krs->id)
            ->where('tahun_matkul_id', $data->tahun_matkul_id)
            ->first();

        if (!$krsMatkul) {
            return response()->json([
                'message' => 'Kode tidak valid!'
            ], 400);
        }
        
        //? Validasi hari
        $today = Carbon::now();
        Carbon::setLocale('id');
        $day = $today->translatedFormat('Y-m-d');

        if ($day != $data->tgl) {
            return response()->json([
                'message' => 'Kode tidak valid!'
            ], 400);
        }

        //? Validasi jam
        if ($today->format('H:i') < $data->jam_mulai || $today->format('H:i') > $data->jam_akhir) {
            return response()->json([
                'message' => 'Kode tidak valid!'
            ], 400);
        }

        $cekSudahPresensi = DB::table('jadwal_presensi')
                    ->where('jadwal_id', $data->id)
                    ->where('mhs_id', Auth::user()->id)
                    ->count();

        if ($cekSudahPresensi > 0) {
            return response()->json([
                'message' => 'Anda sudah melakukan presensi!'
            ], 400);
        }

        DB::table('jadwal_presensi')->insert([
            'jadwal_id' => $data->id,
            'mhs_id' => Auth::user()->id,
            'status' => 'H',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'Presensi berhasil disimpan!'
        ], 200);
    }
}
