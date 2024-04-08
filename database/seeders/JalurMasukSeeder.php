<?php

namespace Database\Seeders;

use App\Models\JalurMasuk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JalurMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JalurMasuk::create([
            'id' => '6',
            'nama' => 'Seleksi Mandiri PTS'
        ]);

        JalurMasuk::create([
            'id' => '5',
            'nama' => 'Seleksi Mandiri PTN'
        ]);
    }
}
