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
        Schema::create('mbkm', function (Blueprint $table) {
            $table->id();
            $table->string('id_neo_feeder')->nullable();
            //? 0 => Personal, 1 => kelompok
            $table->enum('jenis_anggota', [0,1]);
            $table->string('jenis_aktivitas_id');
            $table->foreign('jenis_aktivitas_id')->references('id')->on('jenis_aktivitas');
            $table->foreignId('tahun_semester_id')->constrained('tahun_semester');
            $table->string('judul');
            $table->text('ket')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('sk_tugas')->nullable();
            $table->date('tgl_sk_tugas')->nullable();
            $table->timestamps();
        });

        Schema::create('mbkm_mhs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mbkm_id')->constrained('mbkm');
            $table->foreignId('mhs_id')->constrained('users');
            $table->enum('sync', [0,1])->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('m_b_k_m_s');
    }
};
