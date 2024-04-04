<?php

namespace Database\Seeders;

use App\Models\Wilayah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Wilayah::create([
            'id' => '1',
            'negara_id' => 'ID',
            'nama' => 'Kab. Aceh Besar',
            'id_level_wilayah' => 3,
            'fullNama' => 'Kab. Aceh Besar',
        ]);
    }
}
