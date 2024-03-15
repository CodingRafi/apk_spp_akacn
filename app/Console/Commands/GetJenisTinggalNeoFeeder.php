<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetJenisTinggalNeoFeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neo-feeder:get-jenis-tinggal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Jenis Tinggal NEO Feeder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $res = getDataNeoFeeder([
            "act" => "GetJenisTinggal",
            "filter" => "",
            "order" => "",
            "limit" => "100",
            "offset" => "0"
        ]);

        foreach ($res->json()['data'] as $data) {
            DB::table('jenis_tinggals')->updateOrInsert([
                'id' => $data['id_jenis_tinggal'],
            ], [
                'nama' => $data['nama_jenis_tinggal'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        echo 'Data jenis tinggal berhasil di get!';
    }
}
