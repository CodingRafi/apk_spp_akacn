<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetSemesterNeoFeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neo-feeder:get-semester {tahunAjaranId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get semester NEO Feeder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tahunAjaranId = $this->argument('tahunAjaranId');

        $res = getDataNeoFeeder([
            "act" => "GetSemester",
            "filter" => "id_tahun_ajaran='{$tahunAjaranId}'",
            "order" => "",
            "limit" => "1000",
            "offset" => "0"
        ]);

        if (!$res['status']) {
            $this->error($res['message']);
            return 1;
        }

        foreach ($res['res']->json()['data'] as $data) {
            DB::table('semesters')->updateOrInsert([
                'id' => $data['id_semester'],
            ], [
                'tahun_ajaran_id' => $data['id_tahun_ajaran'],
                'nama' => $data['nama_semester'],
                'semester' => $data['semester'],
                'status' => $data['a_periode_aktif'],
                'tgl_mulai' => Carbon::parse($data['tanggal_mulai'])->format('Y-m-d'),
                'tgl_selesai' => Carbon::parse($data['tanggal_selesai'])->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->info('Data semester berhasil di get!');
        return 0;
    }
}
