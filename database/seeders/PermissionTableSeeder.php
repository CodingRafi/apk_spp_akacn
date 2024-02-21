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

            'view_kelola_pembayaran',
            'add_kelola_pembayaran',
            'edit_kelola_pembayaran',
            'delete_kelola_pembayaran',

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

            'view_pembayaran_lainnya',
            'add_pembayaran_lainnya',
            'edit_pembayaran_lainnya',
            'delete_pembayaran_lainnya',

            'view_kurikulum',
            'add_kurikulum',
            'edit_kurikulum',
            'delete_kurikulum',

            'view_matkul',
            'add_matkul',
            'edit_matkul',
            'delete_matkul',

            'view_ruang',
            'add_ruang',
            'edit_ruang',
            'delete_ruang',

            'view_semester',
            'add_semester',
            'edit_semester',
            'delete_semester',

            'view_kuesioner',
            'add_kuesioner',
            'edit_kuesioner',
            'delete_kuesioner'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
       }
    }
}
