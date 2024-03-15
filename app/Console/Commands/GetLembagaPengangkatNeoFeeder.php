<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetLembagaPengangkatNeoFeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neo-feeder:get-lembaga-pengangkatan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get lembaga pengangkatan NEO Feeder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $res = getDataNeoFeeder([
            "act" => "GetLembagaPengangkat",
            "filter" => "",
            "order" => "",
            "limit" => "100",
            "offset" => "0"
        ]);

        foreach ($res->json()['data'] as $data) {
            DB::table('lembaga_pengangkat')->updateOrInsert([
                'id' => $data['id_lembaga_angkat'],
            ], [
                'nama' => $data['nama_lembaga_angkat'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        echo 'Data lembaga pengangkat berhasil di get!';
    }
}
