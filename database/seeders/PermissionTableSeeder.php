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

            'view_mutu',
            'add_mutu',
            'edit_mutu',
            'delete_mutu',

            'view_materi',
            'add_materi',
            'edit_materi',
            'delete_materi',

            'view_kelola_template_surat',
            'add_kelola_template_surat',
            'edit_kelola_template_surat',
            'delete_kelola_template_surat',

            'view_kelola_gaji',
            'add_kelola_gaji',
            'edit_kelola_gaji',
            'delete_kelola_gaji',

            'view_jenis_kelas',
            'add_jenis_kelas',
            'edit_jenis_kelas',
            'delete_jenis_kelas',

            'view_setting',
            'edit_setting',

            'view_kelola_krs',
            'add_kelola_krs',
            'edit_kelola_krs',
            'delete_kelola_krs',

            'view_neo_feeder',

            'view_kuesioner',
            'add_kuesioner',
            'edit_kuesioner',
            'delete_kuesioner',

            'view_kelola_nilai',
            'add_kelola_nilai',
            'edit_kelola_nilai',
            'delete_kelola_nilai',

            'view_kelola_presensi',
            'add_kelola_presensi',
            'edit_kelola_presensi',
            'delete_kelola_presensi',

            'view_gaji',
            'view_template_surat',

            'view_bimbingan',
            'edit_bimbingan',

            'view_presensi',
            'add_presensi',
            'edit_presensi',
            'delete_presensi',

            'view_pembayaran',
            'add_pembayaran',
            'edit_pembayaran',
            'delete_pembayaran',

            'view_krs',
            'add_krs',
            'edit_krs',
            'delete_krs',

            'view_khs',
            'view_transkrip',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
       }
    }
}
