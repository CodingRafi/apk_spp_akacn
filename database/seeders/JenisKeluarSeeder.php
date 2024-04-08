<?php

namespace Database\Seeders;

use App\Models\JenisKeluar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisKeluarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JenisKeluar::create([
            'id' => '7',
            'jenis' => 'Hilang',
            'apa_mahasiswa' => '0'
        ]);

        JenisKeluar::create([
            'id' => 'z',
            'jenis' => 'Lainnya',
            'apa_mahasiswa' => '0'
        ]);
    }
}
