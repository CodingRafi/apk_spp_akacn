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
        Schema::create('matkuls', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode');
            $table->string('nama');
            $table->char('jenis_matkul', 1)->nullable();
            $table->char('kel_matkul', 1)->nullable();
            $table->integer('sks_mata_kuliah')->nullable();
            $table->integer('sks_tatap_muka')->nullable();
            $table->integer('sks_praktek')->nullable();
            $table->integer('sks_praktek_lapangan')->nullable();
            $table->integer('sks_simulasi')->nullable();
            $table->enum('ada_sap', [0,1])->nullable();
            $table->enum('ada_silabus', [0,1])->nullable();
            $table->enum('ada_bahan_ajar', [0,1])->nullable();
            $table->enum('ada_acara_praktek', [0,1])->nullable();
            $table->enum('ada_diklat', [0,1])->nullable();
            $table->date('tgl_mulai_aktif')->nullable();
            $table->date('tgl_akhir_aktif')->nullable();
            $table->enum('sync', [0,1])->nullable();
            $table->uuid('prodi_id');
            $table->foreign('prodi_id')->references('id')->on('prodi') ;
            $table->timestamps();
        });

        Schema::create('kurikulum_matkul', function (Blueprint $table) {
            $table->uuid('kurikulum_id');
            $table->foreign('kurikulum_id')->references('id')->on('kurikulums');
            $table->uuid('matkul_id');
            $table->foreign('matkul_id')->references('id')->on('matkuls');
            $table->enum('wajib', [0,1]);
        });
        
        Schema::create('matkul_materi', function(Blueprint $table){
            $table->id();
            $table->uuid('matkul_id');
            $table->foreign('matkul_id')->references('id')->on('matkuls')->onDelete('cascade');
            $table->text('materi');
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
        Schema::dropIfExists('matkuls');
    }
};
