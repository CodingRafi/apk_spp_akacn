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
                ->where('negara_id', $row->negara_id)
                ->pluck('id_level_wilayah')
                ->unique();
            
            if (count($cek) > 1) {
                $data = $getWilayah->where('id_level_wilayah', $cek->last());
                
                foreach ($data as $row) {
                    $nama = $row->nama;
                    $id_induk_wilayah = null;

                    while (true) {
                        try {
                            $parent = $getWilayah->where('id', ($id_induk_wilayah ?? $row->id_induk_wilayah))->first();
                            $id_induk_wilayah = $parent->id_induk_wilayah;
                            $nama = $parent->nama . ' - ' . $nama;
                        } catch (\Throwable $th) {
                            break;
                        }

                        if ($id_induk_wilayah == null) {
                            break;
                        }
                    }

                    $row->fullNama = $nama;
                    $row->save();
                }
            }
        }
    }
}
