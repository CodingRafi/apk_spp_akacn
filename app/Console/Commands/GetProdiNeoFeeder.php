<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetProdiNeoFeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neo-feeder:get-prodi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get prodi NEO Feeder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $res = getDataNeoFeeder([
            "act" => "GetProdi",
            "filter" => "",
            "order" => "",
            "limit" => "1000",
            "offset" => "0"
        ]);

        foreach ($res->json()['data'] as $data) {
            DB::table('prodi')->updateOrInsert([
                'id' => $data['id_prodi'],
            ], [
                'kode' => $data['kode_program_studi'],
                'nama' => $data['nama_program_studi'],
                'akreditas' => $data['status'],
                'jenjang_id' => $data['id_jenjang_pendidikan'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        echo 'Data prodi berhasil di get!';
    }
}
