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
        //? VIEW REKAP PEMBAYARAN SEMESTER
        // DB::statement("
        //     CREATE VIEW rekap_pembayaran_semester AS
        //     SELECT u.id as user_id, sum(p.nominal) total, p.tahun_semester_id
        //     FROM users as u
        //     inner join pembayarans p on p.mhs_id = u.id
        //     where p.status = 'diterima'
        //     and p.tahun_semester_id is not null
        //     group by u.id, p.tahun_semester_id;
        // ");

        // //? VIEW REKAP PEMBAYARAN LAINNYA
        // DB::statement("
        //     CREATE VIEW rekap_pembayaran_lain AS
        //     SELECT u.id as user_id, sum(p.nominal) total, p.tahun_pembayaran_lain_id
        //     FROM users as u
        //     inner join pembayarans p on p.mhs_id = u.id
        //     where p.status = 'diterima'
        //     and p.tahun_pembayaran_lain_id is not null
        //     group by u.id, p.tahun_pembayaran_lain_id;
        // ");

        // //? VIEW REKAP POTONGAN
        // DB::statement("
        //     create view rekap_potongan as
        //     select u.id, sum(pta.nominal) as total, pta.tahun_semester_id, pta.tahun_pembayaran_lain_id from users u
        //     inner join potongan_mhs pm on pm.mhs_id = u.id
        //     inner join potongan_tahun_ajaran pta on pta.id = pm.potongan_tahun_ajaran_id 
        //     inner join potongans p on p.id = pta.potongan_id
        //     group by u.id, pta.type, pta.tahun_semester_id, pta.tahun_pembayaran_lain_id
        // ");

        // //? VIEW REKAP PEMBAYARAN
        // DB::statement("
        //     create view rekap_pembayaran as
        //     select
        //     rps.user_id,
        //     s.nama,
        //     'semester' as type,
        //     rps.tahun_semester_id as untuk,
        //     tp.nominal as harus,
        //     rps.total as total_pembayaran,
        //     COALESCE(rp.total, 0) AS potongan,
        //     GREATEST(
        //         tp.nominal - (rps.total + COALESCE(rp.total, 0)),
        //         0
        //     ) AS sisa
        // from
        //     rekap_pembayaran_semester rps
        //     inner join tahun_pembayaran tp on tp.tahun_semester_id = rps.tahun_semester_id
        //     inner join rekap_potongan rp on (
        //         rp.id = rps.user_id
        //         and rp.tahun_semester_id = rps.tahun_semester_id
        //     )
        //     inner join tahun_semester ts on ts.id = rps.tahun_semester_id
        //     inner join semesters s on s.id = ts.semester_id
        // union
        // select
        //     rpl.user_id,
        //     pl.nama,
        //     'lainnya' as type,
        //     rpl.tahun_pembayaran_lain_id as untuk,
        //     tpl.nominal as harus,
        //     rpl.total as total_pembayaran,
        //     COALESCE(rp.total, 0) AS potongan,
        //     GREATEST(
        //         tpl.nominal - (rpl.total + COALESCE(rp.total, 0)),
        //         0
        //     ) AS sisa
        // from
        //     rekap_pembayaran_lain rpl
        //     inner join tahun_pembayaran_lain tpl on tpl.id = rpl.tahun_pembayaran_lain_id
        //     inner join rekap_potongan rp on (
        //         rp.id = rpl.user_id
        //         and rp.tahun_pembayaran_lain_id = rpl.tahun_pembayaran_lain_id
        //     )
        //     inner join pembayaran_lainnyas pl on pl.id = tpl.pembayaran_lainnya_id
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
    }
};
