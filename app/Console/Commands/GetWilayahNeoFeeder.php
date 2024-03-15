<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetWilayahNeoFeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neo-feeder:get-wilayah';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get wilayah NEO Feeder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $res = getDataNeoFeeder([
            "act" => "GetWilayah",
            "filter" => "",
            "order" => "",
            "limit" => "10000",
            "offset" => "0"
        ]);
        
        foreach ($res->json()['data'] as $data) {
            DB::table('wilayahs')->updateOrInsert([
                'id' => $data['id_wilayah'],
            ], [
                'nama' => $data['nama_wilayah'],
                'id_level_wilayah' => $data['id_level_wilayah'],
                'negara_id' => $data['id_negara'],
                'id_induk_wilayah' => $data['id_induk_wilayah'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        echo 'Data wilayah berhasil di get!';
    }
}
