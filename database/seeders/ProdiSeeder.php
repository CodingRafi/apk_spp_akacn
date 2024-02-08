<?php

namespace Database\Seeders;

use App\Models\Prodi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Prodi::create([
            'id' => generateUuid(),
            'kode' => "A2d3",
            'nama' => "Kimia",
            'akreditas' => 'A',
            'jenjang_id' => 'ef267b89-7e88-47d1-8cfc-0fa88cff2b40',
            'status' => '1',
            'jml_semester' => 8
        ]);
    }
}
