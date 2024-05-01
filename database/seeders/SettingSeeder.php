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
        // DB::table('settings')->insert([
        //     'nama' => 'Uang Tranport Dosen',
        //     'type' => 'number',
        //     'value' => '15000',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // DB::table('settings')->insert([
        //     'nama' => 'URL Neofeeder',
        //     'type' => 'text',
        //     'value' => '',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // DB::table('settings')->insert([
        //     'nama' => 'Uang Tranport Asdos',
        //     'type' => 'number',
        //     'value' => '15000',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // DB::table('settings')->insert([
        //     'nama' => 'Uang Teori Dosen 1 SKS',
        //     'type' => 'number',
        //     'value' => '15000',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // DB::table('settings')->insert([
        //     'nama' => 'Uang Teori Asdos 1 SKS',
        //     'type' => 'number',
        //     'value' => '15000',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        DB::table('settings')->insert([
            'nama' => 'Uang Praktek Dosen 1 SKS',
            'type' => 'number',
            'value' => '15000',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('settings')->insert([
            'nama' => 'Uang Praktek Asdos 1 SKS',
            'type' => 'number',
            'value' => '15000',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
