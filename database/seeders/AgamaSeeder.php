<?php

namespace Database\Seeders;

use App\Models\Agama;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('agamas')->updateOrInsert([
            'id' => 98,
        ], [
            'nama' => 'Tidak diisi',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
