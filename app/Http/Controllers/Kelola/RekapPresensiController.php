<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RekapPresensiController extends Controller
{
    public function index()
    {
        $tahunAjarans = DB::table('tahun_ajarans')->get();
        $prodis = DB::table('prodi')->get();
        return view('kelola.rekap_presensi.index', compact('prodis', 'tahunAjarans'));
    }

    public function getMatkul($tahun_ajaran_id){
        $matkuls = DB::table('tahun_matkul')
        ->select('tahun_matkul.id', 'matkuls.nama')
        ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
        ->join('tahun_matkul_dosen', function($q){
            $q->on('tahun_matkul_dosen.tahun_matkul_id', '=', 'tahun_matkul.id')
                ->when(!Auth::user()->hasRole('admin'), function ($q2) {
                    $q2->where('tahun_matkul_dosen.dosen_id', Auth::user()->id);
                });
        })
        ->where('tahun_matkul.tahun_ajaran_id', $tahun_ajaran_id)
        ->where('tahun_matkul.prodi_id', request('prodi_id'))
        ->get();
        
        return response()->json([
            'data' => $matkuls
        ], 200);
    }

    public function getPresensi()
    {
        $mahasiswas = DB::table('krs')
            ->select('users.name', 'users.login_key', 'users.id')
            ->join('krs_matkul', function($q){
                $q->on('krs_matkul.krs_id', '=', 'krs.id')
                    ->where('krs_matkul.tahun_matkul_id', request('tahun_matkul_id'));
            })
            ->join('users', 'krs.mhs_id', '=', 'users.id')
            ->distinct('users.id')
            ->get()
            ->map(function ($mahasiswa) {
                $getPresensi = DB::table('jadwal')
                    ->select('jadwal.id as jadwal_id', 'jadwal_presensi.status', 'jadwal_presensi.mhs_id', 'jadwal.jenis_ujian', 'jadwal.type')
                    ->leftJoin('jadwal_presensi', function ($join) use ($mahasiswa) {
                        $join->on('jadwal_presensi.jadwal_id', '=', 'jadwal.id')
                            ->where('jadwal_presensi.mhs_id', '=', $mahasiswa->id);
                    })
                    ->where('jadwal.tahun_matkul_id', request('tahun_matkul_id'))
                    ->orderBy('jadwal.tgl', 'ASC')
                    ->get();

                $presensiPertemuan = $getPresensi->filter(function ($data) {
                    return $data->type == 'pertemuan';
                })->values();

                $presensi = [];

                for ($i = 0; $i < config('services.max_pertemuan'); $i++) {
                    $presensi[$i] = [
                        'jadwal_id' => $presensiPertemuan->get($i)->jadwal_id ?? null,
                        'status' => $presensiPertemuan->get($i)->status ?? null
                    ];
                }

                $presensiUjian = $getPresensi->filter(function ($data) {
                    return $data->type == 'ujian';
                })->values();

                $resPresensi = [];

                foreach (config('services.ujian') as $key => $jenis) {
                    $presensiCheck = $presensiUjian->firstWhere('jenis_ujian', $jenis['key']);
                    $sliceData = array_slice($presensi, $jenis['indexStart'], (7 * ($key + 1)));
                    $sliceData[] = [
                        'jadwal_id' => $presensiCheck ? $presensiCheck->jadwal_id : null,
                        'status' => $presensiCheck ? $presensiCheck->status : null,
                        'jenis' => $jenis['key']
                    ];
                    $resPresensi = array_merge($resPresensi, $sliceData);
                }

                $mahasiswa->presensi = $resPresensi;
                return $mahasiswa;
            });

        return response()->json([
            'data' => $mahasiswas
        ], 200);
    }
}
