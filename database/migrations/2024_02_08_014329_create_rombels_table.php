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
        Schema::create('rombels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('dosen_pa_id')->constrained('users');
            $table->string('nama');
            $table->uuid('prodi_id');
            $table->foreign('prodi_id')->references('id')->on('prodi')->onDelete('cascade');
            $table->uuid('tahun_ajaran_id');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans')->onDelete('cascade');
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
        Schema::dropIfExists('rombels');
    }
};
