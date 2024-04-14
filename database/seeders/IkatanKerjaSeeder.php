<?php

namespace Database\Seeders;

use App\Models\IkatanKerja;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IkatanKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        IkatanKerja::create([
            'id' => 'X',
            'nama' => '6826253'
        ]);
    }
}
