<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetPenghasilanNeoFeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neo-feeder:get-penghasilan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get penghasilan NEO Feeder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $res = getDataNeoFeeder([
            "act" => "GetPenghasilan",
            "filter" => "",
            "order" => "",
            "limit" => "100",
            "offset" => "0"
        ]);

        foreach ($res->json()['data'] as $data) {
            DB::table('penghasilans')->updateOrInsert([
                'id' => $data['id_penghasilan'],
            ], [
                'nama' => $data['nama_penghasilan'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        echo 'Data penghasilan berhasil di get!';
    }
}
