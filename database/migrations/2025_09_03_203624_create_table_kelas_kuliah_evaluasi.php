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
        Schema::create('kelas_kuliah_evaluasi', function (Blueprint $table) {
            $table->id();
            $table->string('id_kelas_kuliah');
            $table->string('id_komp_eval');
            $table->string('id_jns_eval');
            $table->string('nm_jns_eval');
            $table->string('komponen_evaluasi');
            $table->string('nama_inggris');
            $table->string('bobot');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kelas_kuliah_evaluasi');
    }
};
