<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetAgamaNeoFeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neo-feeder:get-agama';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get agama NEO Feeder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $res = getDataNeoFeeder([
            "act" => "GetAgama",
            "filter" => "",
            "order" => "",
            "limit" => "10",
            "offset" => "0"
        ]);

        foreach ($res->json()['data'] as $data) {
            DB::table('agamas')->updateOrInsert([
                'id' => $data['id_agama'],
            ],[
                'nama' => $data['nama_agama'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        echo 'Data agama berhasil di get!';
    }
}
