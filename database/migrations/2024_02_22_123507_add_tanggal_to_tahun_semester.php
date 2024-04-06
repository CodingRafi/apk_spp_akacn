<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('tahun_semester', function (Blueprint $table) {
            $table->date('tgl_mulai_krs');
            $table->date('tgl_akhir_krs');
            $table->date('tgl_mulai');
            $table->date('tgl_akhir');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tahun_semester', function (Blueprint $table) {
            $table->dropColumn('tgl_mulai_krs');
            $table->dropColumn('tgl_akhir_krs');
            $table->dropColumn('tgl_mulai');
            $table->dropColumn('tgl_akhir');
        });
    }
};
