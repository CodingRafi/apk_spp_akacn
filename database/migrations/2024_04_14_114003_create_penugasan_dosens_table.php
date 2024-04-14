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
        Schema::create('penugasan_dosens', function (Blueprint $table) {
            $table->id();
            $table->string('id_dosen');
            $table->string('id_registrasi_dosen');
            $table->string('tahun_ajaran_id');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans');
            $table->uuid('prodi_id')->nullable();
            $table->foreign('prodi_id')->references('id')->on('prodi');
            $table->string('nomor_surat_tugas')->nullable();
            $table->date('tanggal_surat_tugas')->nullable();
            $table->date('mulai_surat_tugas')->nullable();
            $table->date('tgl_create')->nullable();
            $table->date('tgl_ptk_keluar')->nullable();
            $table->string('status_pegawai_id')->nullable();
            $table->foreign('status_pegawai_id')->references('id')->on('status_pegawais');
            $table->string('jenis_keluar_id')->nullable();
            $table->foreign('jenis_keluar_id')->references('id')->on('jenis_keluars');
            $table->string('ikatan_kerja_id')->nullable();
            $table->foreign('ikatan_kerja_id')->references('id')->on('ikatan_kerjas');
            $table->enum('a_sp_homebase', [0,1]);
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
        Schema::dropIfExists('penugasan_dosens');
    }
};
