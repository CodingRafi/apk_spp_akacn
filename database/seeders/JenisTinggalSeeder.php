<?php

namespace Database\Seeders;

use App\Models\JenisTinggal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisTinggalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JenisTinggal::create([
            'id' => '1',
            'nama' => 'Asrama'
        ]);
    }
}
