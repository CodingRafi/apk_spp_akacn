<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetJenjangNeoFeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neo-feeder:get-jenjang';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get jenjang NEO feeder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $res = getDataNeoFeeder([
            "act" => "GetJenjangPendidikan",
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
            DB::table('jenjangs')->updateOrInsert([
                'id' => $data['id_jenjang_didik'],
            ], [
                'nama' => $data['nama_jenjang_didik'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->info('Data jenjang berhasil di get!');
        return 0;
    }
}
