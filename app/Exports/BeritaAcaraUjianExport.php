<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class BeritaAcaraUjianExport implements FromView
{
   protected $jadwalId;

    public function __construct($jadwalId)
    {
        $this->jadwalId = $jadwalId;
    }

    public function view(): View
    {
        $jadwal = DB::table('jadwal')
            ->select([
                'jadwal.presensi_mulai',
                'ruangs.nama as ruang',
                'matkuls.nama as matkul',
                'jadwal.tingkat',
                'jadwal.ket',
                'jadwal.status_ujian',
                'sifat_ujians.nama as sifat_ujian',
                'jadwal.id',
                'jadwal.tahun_matkul_id',
                DB::raw("GROUP_CONCAT(u.name SEPARATOR ', ') AS dosen"),
                'jadwal.tgl'
            ])
            ->join('tahun_matkul', 'tahun_matkul.id', '=', 'jadwal.tahun_matkul_id')
            ->join('tahun_matkul_dosen as tmd', 'tmd.tahun_matkul_id', '=', 'tahun_matkul.id')
            ->join('users as u', 'u.id', '=', 'tmd.dosen_id')
            ->join('matkuls', 'matkuls.id', '=', 'tahun_matkul.matkul_id')
            ->leftJoin('ruangs', 'ruangs.id', '=', 'jadwal.ruang_id')
            ->leftJoin('sifat_ujians', 'sifat_ujians.id', '=', 'jadwal.sifat_ujian_id')
            ->where('jadwal.id', $this->jadwalId)
            ->groupBy([
                'jadwal.presensi_mulai',
                'ruangs.nama',
                'matkuls.nama',
                'jadwal.tingkat',
                'jadwal.ket',
                'jadwal.status_ujian',
                'sifat_ujians.nama',
                'jadwal.id',
                'jadwal.tahun_matkul_id',
                'jadwal.tgl'
            ])
            ->first();

        $presensi = DB::table('krs')
            ->select('krs.mhs_id', 'jadwal_presensi.status')
            ->join('krs_matkul', function($q) use ($jadwal) {
                $q->on('krs.id', '=', 'krs_matkul.krs_id')
                    ->where('krs_matkul.tahun_matkul_id', $jadwal->tahun_matkul_id);
            })
            ->leftJoin('jadwal_presensi', function ($join) use ($jadwal) {
                $join->on('jadwal_presensi.mhs_id', 'krs.mhs_id')
                    ->where('jadwal_presensi.jadwal_id', $jadwal->id);
            })
            ->get();

        $totalHarusHadir = $presensi->count();
        $totalTidakHadir = $presensi->where('status', '!=', 'H')->count();

        return view('kelola.jadwal.tahun_matkul.berita_acara.export', compact('jadwal', 'totalHarusHadir', 'totalTidakHadir'));
    }
}
