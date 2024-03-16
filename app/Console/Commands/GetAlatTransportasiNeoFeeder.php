<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetAlatTransportasiNeoFeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neo-feeder:get-alat-transportasi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get alat transportasi NEO Feeder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $res = getDataNeoFeeder([
            "act" => "GetAlatTransportasi",
            "filter" => "",
            "order" => "",
            "limit" => "100",
            "offset" => "0"
        ]);

        if (!$res['status']) {
            $this->error($res['message']);
            return 1;
        }

        foreach ($res['res']->json()['data'] as $data) {
            DB::table('alat_transportasis')->updateOrInsert([
                'id' => $data['id_alat_transportasi'],
            ],[
                'nama' => $data['nama_alat_transportasi'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->info('Data alat transportasi berhasil di get!');
        return 0;
    }
}
