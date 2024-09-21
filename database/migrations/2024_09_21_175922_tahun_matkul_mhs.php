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
        //? ini table untuk manambahkan mahasiswa ngulang dll
        Schema::create('tahun_matkul_mhs', function(Blueprint $table){
            $table->id();
            $table->foreignId('mhs_id')->constrained('users');
            $table->foreignId('tahun_matkul_id')->constrained('tahun_matkul');
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
        Schema::dropIfExists('tahun_matkul_mhs');
    }
};
