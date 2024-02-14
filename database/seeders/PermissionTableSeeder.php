<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'view_users',
            'add_users',
            'edit_users',
            'delete_users',

            'view_roles',
            'edit_roles',

            'view_tahun_ajaran',
            'add_tahun_ajaran',
            'edit_tahun_ajaran',
            'delete_tahun_ajaran',

            'view_prodi',
            'add_prodi',
            'edit_prodi',
            'delete_prodi',

            'view_whitelist_ip',
            'add_whitelist_ip',
            'delete_whitelist_ip',

            'view_biaya',
            'add_biaya',
            'edit_biaya',
            'delete_biaya',

            'view_kelola_pembayaran',
            'edit_kelola_pembayaran',

            'view_pembayaran',
            'add_pembayaran',
            'edit_pembayaran',
            'delete_pembayaran',

            'view_potongan',
            'add_potongan',
            'edit_potongan',
            'delete_potongan',

            'view_rombel',
            'add_rombel',
            'edit_rombel',
            'delete_rombel',

            'view_biaya_lainnya',
            'add_biaya_lainnya',
            'edit_biaya_lainnya',
            'delete_biaya_lainnya',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
       }
    }
}
