<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetKewarganegaraanNeoFeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neo-feeder:get-kewarganegaraan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get kewarganegaraan NEO feeder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $res = getDataNeoFeeder([
            "act" => "GetNegara",
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
            DB::table('kewarganegaraans')->updateOrInsert([
                'id' => $data['id_negara'],
            ], [
                'nama' => $data['nama_negara'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->info('Data kewarganegaraan berhasil di get!');
        return 0;
    }
}
