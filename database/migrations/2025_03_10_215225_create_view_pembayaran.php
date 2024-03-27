<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // DB::statement("DROP VIEW IF EXISTS rekap_pembayaran_semester");
        // DB::statement("DROP VIEW IF EXISTS rekap_pembayaran_lain");
        // DB::statement("DROP VIEW IF EXISTS rekap_potongan");
        // DB::statement("DROP VIEW IF EXISTS rekap_pembayaran_tambahan");
        // DB::statement("DROP VIEW IF EXISTS rekap_pembayaran");

        // //? VIEW REKAP PEMBAYARAN SEMESTER
        // DB::statement("
        //     CREATE VIEW rekap_pembayaran_semester AS
        //     SELECT u.id as user_id, sum(p.nominal) total, p.tahun_pembayaran_id
        //     FROM users as u
        //     inner join pembayarans p on p.mhs_id = u.id
        //     where p.status = 'diterima'
        //     and p.tahun_pembayaran_id is not null
        //     group by u.id, p.tahun_pembayaran_id;
        // ");

        // // //? VIEW REKAP PEMBAYARAN LAINNYA
        // DB::statement("
        //     CREATE VIEW rekap_pembayaran_lain AS
        //     SELECT u.id as user_id, sum(p.nominal) total, p.tahun_pembayaran_lain_id
        //     FROM users as u
        //     inner join pembayarans p on p.mhs_id = u.id
        //     where p.status = 'diterima'
        //     and p.tahun_pembayaran_lain_id is not null
        //     group by u.id, p.tahun_pembayaran_lain_id;
        // ");

        // // //? VIEW REKAP POTONGAN
        // DB::statement("
        //     create view rekap_potongan as
        //     select u.id, sum(pta.nominal) as total, pta.tahun_semester_id, pta.tahun_pembayaran_lain_id from users u
        //     inner join potongan_mhs pm on pm.mhs_id = u.id
        //     inner join potongan_tahun_ajaran pta on pta.id = pm.potongan_tahun_ajaran_id 
        //     inner join potongans p on p.id = pta.potongan_id
        //     group by u.id, pta.type, pta.tahun_semester_id, pta.tahun_pembayaran_lain_id
        // ");

        // DB::statement("
        //     create view rekap_pembayaran_tambahan as
        //     select
        //         mhs_id as user_id,
        //         sum(nominal) as total,
        //         tahun_semester_id,
        //         tahun_pembayaran_lain_id
        //     from pembayaran_tambahans pt
        //     group by mhs_id, tahun_semester_id, tahun_pembayaran_lain_id
        // ");

        // // //? VIEW REKAP PEMBAYARAN
        // DB::statement("
        //     create view rekap_pembayaran as
        //     select
        //         u.id as user_id,
        //         s.nama,
        //         'semester' as type,
        //         tp.id as untuk,
        //         COALESCE(tp.nominal , 0) as harus,
        //         COALESCE(rps.total, 0) as total_pembayaran,
        //         COALESCE(rp.total, 0) AS potongan,
        //         COALESCE(rpt.total, 0) AS tambahan,
        //         GREATEST((tp.nominal + COALESCE(rpt.total, 0)) - (COALESCE(rps.total, 0) + COALESCE(rp.total, 0)),0) AS sisa 
        //     from users u
        //     inner join profile_mahasiswas pm ON pm.user_id = u.id
        //     inner join tahun_semester as ts on ts.prodi_id = pm.prodi_id and ts.tahun_ajaran_id = pm.tahun_masuk_id
        //     inner join semesters s on ts.semester_id = s.id
        //     inner join tahun_pembayaran tp on ts.id = tp.tahun_semester_id
        //     left join rekap_pembayaran_semester rps on rps.user_id = u.id and rps.tahun_pembayaran_id = tp.id
        //     left join rekap_potongan rp on rp.id = u.id and rp.tahun_semester_id = ts.id
        //     left join rekap_pembayaran_tambahan rpt on rpt.user_id = u.id and rpt.tahun_semester_id = ts.id
        //     union
        //     select
        //         u.id as user_id,
        //         pl.nama,
        //         'lainnya' as type,
        //         tpl.id as untuk,
        //         COALESCE(tpl.nominal , 0) as harus,
        //         COALESCE(rpl.total, 0) as total_pembayaran,
        //         COALESCE(rp.total, 0) AS potongan,
        //         COALESCE(rpt.total, 0) AS tambahan, 
        //         GREATEST((tpl.nominal + COALESCE(rpt.total, 0)) - (COALESCE(rpl.total, 0) + COALESCE(rp.total, 0)),0) AS sisa 
        //     from users u
        //     inner join profile_mahasiswas pm ON pm.user_id = u.id
        //     inner join tahun_pembayaran_lain tpl on tpl.prodi_id = pm.prodi_id and tpl.tahun_ajaran_id = pm.tahun_masuk_id
        //     inner join pembayaran_lainnyas pl on pl.id = tpl.pembayaran_lainnya_id
        //     left join rekap_pembayaran_lain rpl on rpl.tahun_pembayaran_lain_id = tpl.id 
        //     left join rekap_potongan rp on rp.id = u.id and rp.tahun_pembayaran_lain_id  = tpl.id
        //     left join rekap_pembayaran_tambahan rpt on rpt.user_id = u.id and rpt.tahun_pembayaran_lain_id = tpl.id
        // ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS rekap_pembayaran_semester");
        DB::statement("DROP VIEW IF EXISTS rekap_pembayaran_lain");
        DB::statement("DROP VIEW IF EXISTS rekap_potongan");
        DB::statement("DROP VIEW IF EXISTS rekap_pembayaran");
        DB::statement("DROP VIEW IF EXISTS rekap_pembayaran_tambahan");
    }
};
