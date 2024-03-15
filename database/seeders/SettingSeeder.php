<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'nama' => 'Uang Tranport',
            'type' => 'number',
            'value' => '15000',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('settings')->insert([
            'nama' => 'URL Neofeeder',
            'type' => 'text',
            'value' => '',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
