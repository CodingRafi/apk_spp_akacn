<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncJadwalUjian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:jadwal-ujian';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync jadwal ujian';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $matkuls = DB::table('jadwal as j')
            ->select('m.nama', 'm.id as matkul_id', 'tm.id as tahun_matkul_id', 'j.id as jadwal_id')
            ->join('tahun_matkul as tm', 'tm.id', 'j.tahun_matkul_id')
            ->join('matkuls as m', 'm.id', 'tm.matkul_id')
            ->where('j.tgl', '>=', '2025-10-26')
            ->where('j.type', 'ujian')
            ->get();

        $materi_id = [];

        //? Insert Materi
        foreach ($matkuls as $matkul) {
            $type = preg_match('/praktik(um)?/i', $matkul->nama)
                ? 'praktek'
                : 'teori';

            DB::table('matkul_materi')->updateOrInsert(
                [
                    'matkul_id' => $matkul->matkul_id,
                    'materi'    => 'UTS'
                ],
                [
                    'type'       => $type,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            $id = DB::table('matkul_materi')
                ->where('matkul_id', $matkul->matkul_id)
                ->where('materi', 'UTS')
                ->value('id');

            $materi_id[$matkul->tahun_matkul_id] = $id;
        }

        foreach ($matkuls as $matkul) {
            DB::table('jadwal')
                ->where('id', $matkul->jadwal_id)
                ->update([
                    'materi_id' => $materi_id[$matkul->tahun_matkul_id],
                    'materi'    => 'UTS'
                ]);
        }

        $this->info('Sync jadwal ujian selesai');
    }
}
