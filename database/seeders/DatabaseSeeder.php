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
        $this->call(PermissionTableSeeder::class);
        $this->call(JenjangSeeder::class);
        $this->call(LembagaPengangkatSeeder::class);
        $this->call(AgamaSeeder::class);
        $this->call(ProdiSeeder::class);
        $this->call(UserSeeder::class);
    }
}
