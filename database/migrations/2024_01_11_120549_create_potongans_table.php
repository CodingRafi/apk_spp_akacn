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
        Schema::create('potongans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->uuid('prodi_id');
            $table->foreign('prodi_id')->references('id')->on('prodi')->onDelete('cascade');
            $table->integer('semester');
            $table->timestamps();
        });

        Schema::create('potongan_tahun_ajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('potongan_id')->constrained('potongans');
            $table->string('nominal');
            $table->string('ket');
            $table->uuid('tahun_ajaran_id');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('potongan_mhs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('potongan_id')->constrained('potongans');
            $table->foreignId('mhs_id')->constrained('users');
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
        Schema::dropIfExists('potongans');
        Schema::dropIfExists('potongan_mhs');
    }
};
