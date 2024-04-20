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
        Schema::create('mbkm_dosen_penguji', function (Blueprint $table) {
            $table->id();
            $table->string('id_uji_neo_feeder')->nullable();
            $table->foreignId('mbkm_id')->constrained('mbkm');
            $table->foreignId('dosen_id')->constrained('users');
            $table->string('kategori_kegiatan_id');
            $table->foreign('kategori_kegiatan_id')->references('id')->on('kategori_kegiatans');
            $table->string('penguji_ke');
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
        Schema::dropIfExists('mbkm_dosen_penguji');
    }
};
