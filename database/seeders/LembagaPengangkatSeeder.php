<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LembagaPengangkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lembaga_pengangkats')->insert([
            'id' => '1',
            'nama' => 'Kementerian Riset, Teknologi dan Pendidikan Tinggi'
        ]);
    }
}
