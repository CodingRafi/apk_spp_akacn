<?php

namespace Database\Seeders;

use App\Models\Kewarganegaraan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KewarganegaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Kewarganegaraan::create([
            'id' => 'ID',
            'nama' => 'Indonesia',
        ]);
    }
}
