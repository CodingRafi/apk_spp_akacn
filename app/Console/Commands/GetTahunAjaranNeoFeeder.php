<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetTahunAjaranNeoFeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neo-feeder:get-tahun-ajaran';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Tahun Ajaran';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $res = getDataNeoFeeder([
            "act" => "GetTahunAjaran",
            "filter" => "",
            "order" => "",
            "limit" => "1000",
            "offset" => "0"
        ]);

        if (!$res['status']) {
            $this->error($res['message']);
            return 1;
        }

        foreach ($res['res']->json()['data'] as $data) {
            DB::table('tahun_ajarans')->updateOrInsert([
                'id' => $data['id_tahun_ajaran'],
            ], [
                'nama' => $data['nama_tahun_ajaran'],
                'status' => $data['a_periode_aktif'],
                'tgl_mulai' => Carbon::parse($data['tanggal_mulai'])->format('Y-m-d'),
                'tgl_selesai' => Carbon::parse($data['tanggal_selesai'])->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->info('Data tahun ajaran berhasil di get!');
        return 0;
    }
}
