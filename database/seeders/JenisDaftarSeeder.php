<?php

namespace Database\Seeders;

use App\Models\JenisDaftar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisDaftarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JenisDaftar::create([
            'id' => 0,
            'nama' => 'Lainnya'
        ]);
    }
}
