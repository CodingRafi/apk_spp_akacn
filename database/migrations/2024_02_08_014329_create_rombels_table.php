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
            $table->id();
            $table->string('nama');
            $table->uuid('prodi_id');
            $table->foreign('prodi_id')->references('id')->on('prodi')->onDelete('cascade');
            $table->timestamps();
        });
        
        Schema::create('rombel_tahun_ajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rombel_id')->constrained('rombels');
            $table->uuid('tahun_masuk_id');
            $table->foreign('tahun_masuk_id')->references('id')->on('tahun_ajarans')->onDelete('cascade');
            $table->foreignId('dosen_pa_id')->constrained('users');
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
