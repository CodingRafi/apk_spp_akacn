<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(AlatTransportasiSeeder::class);
        // $this->call(JenisTinggalSeeder::class);
        // $this->call(JenjangSeeder::class);
        // $this->call(KewarganegaraanSeeder::class);
        // $this->call(LembagaPengangkatSeeder::class);
        // $this->call(PekerjaanSeeder::class);
        // $this->call(PenghasilanSeeder::class);
        // $this->call(WilayahSeeder::class);
        // $this->call(ProdiSeeder::class);
        $this->call(LembagaPengangkatSeeder::class);
        $this->call(JalurMasukSeeder::class);
        $this->call(JenisDaftarSeeder::class);
        $this->call(JenisKeluarSeeder::class);
        $this->call(TahunAjaranSeeder::class);
        $this->call(StatusPegawaiSeeder::class);
        $this->call(IkatanKerjaSeeder::class);
        $this->call(AgamaSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(UserSeeder::class);
    }
}
