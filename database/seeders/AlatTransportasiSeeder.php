<?php

namespace Database\Seeders;

use App\Models\AlatTransportasi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlatTransportasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AlatTransportasi::create([
            'id' => '1',
            'nama' => 'sepeda'
        ]);
    }
}
