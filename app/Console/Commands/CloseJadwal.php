<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CloseJadwal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jadwal:close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close all jadwal';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::table('jadwal')
        ->whereNotNull('presensi_mulai')
        ->update([
            'presensi_selesai' => now(),
            'status_close' => '2',
            'approved' => '1'
        ]);
        Log::info('Command jadwal:close berhasil dijalankan pada ' . now());
        return Command::SUCCESS;
    }
}
