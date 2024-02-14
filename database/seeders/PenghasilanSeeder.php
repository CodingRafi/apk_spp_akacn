<?php

namespace Database\Seeders;

use App\Models\Penghasilan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PenghasilanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Penghasilan::create([
            'id' => '1',
            'nama' => 'Kerja'
        ]);
    }
}
