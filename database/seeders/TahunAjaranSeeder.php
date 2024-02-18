<?php

namespace Database\Seeders;

use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TahunAjaran::create([
            'id' => '2023',
            'nama' => '2023/2024',
            'status' => 1,
            'tgl_mulai' => '2023/01/01',
            'tgl_selesai' => '2024/01/01',
        ]);
        
        Semester::create([
            'id' => '20231',
            'tahun_ajaran_id' => '2023',
            'nama' => '2023/2024 Ganjil',
            'semester' => '1',
            'tgl_mulai' => '2023/01/01',
            'tgl_selesai' => '2024/03/01',
            'jatah_sks' => '200'
        ]);
    }
}
