<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetPekerjaanNeoFeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neo-feeder:get-pekerjaan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get pekerjaan NEO Feeder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $res = getDataNeoFeeder([
            "act" => "GetPekerjaan",
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
            DB::table('pekerjaans')->updateOrInsert([
                'id' => $data['id_pekerjaan'],
            ], [
                'nama' => $data['nama_pekerjaan'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        $this->info('Data pekerjaan berhasil di get!');
        return 0;
    }
}
