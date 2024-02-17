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
            $table->timestamps();
        });

        Schema::create('potongan_tahun_ajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('potongan_id')->constrained('potongans');
            $table->string('nominal');
            $table->string('ket');
            $table->foreignId('tahun_semester_id')->constrained('tahun_semester');
            $table->enum('publish', [0,1]);
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
