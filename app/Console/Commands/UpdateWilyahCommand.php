<?php

namespace App\Console\Commands;

use App\Models\Wilayah;
use Illuminate\Console\Command;

class UpdateWilyahCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-wilayah:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Wilayah';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $getWilayah = Wilayah::all();

        foreach ($getWilayah->where('id_level_wilayah', '0') as $row) {
            $cek = $getWilayah
                ->where('id_induk_wilayah', $row->id)
                ->pluck('id_level_wilayah')
                ->unique();

            dd($cek);
            
            if (count($cek) > 1) {
                $data = $getWilayah->where('id_level_wilayah', $cek->last());
                dd($data);
                foreach ($data as $key => $row) {
                    $parent = $getWilayah->where('id', $row->id_induk_wilayah)->first();
                    $data[$key]->nama = $parent->nama . ' - ' . $row->nama;
                }
            }
        }
    }
}
