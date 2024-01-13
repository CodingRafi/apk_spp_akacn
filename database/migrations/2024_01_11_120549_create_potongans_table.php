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
            $table->foreignId('prodi_id')->constrained('prodi');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans');
            $table->string('nominal');
            $table->text('ket');
            $table->timestamps();
        });

        Schema::create('potongan_mhs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('potongan_id')->constrained('potongans');
            $table->foreignId('mhs_id')->constrained('mahasiswas');
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
