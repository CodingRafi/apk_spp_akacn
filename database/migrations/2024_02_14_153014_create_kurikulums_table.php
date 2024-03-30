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
        Schema::create('kurikulums', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->integer('jml_sks_lulus');
            $table->integer('jml_sks_wajib');
            $table->integer('jml_sks_pilihan');
            $table->integer('jml_sks_mata_kuliah_wajib')->nullable();
            $table->integer('jml_sks_mata_kuliah_pilihan')->nullable();
            $table->uuid('prodi_id');
            $table->foreign('prodi_id')->references('id')->on('prodi');
            $table->string('semester_id');
            $table->foreign('semester_id')->references('id')->on('semesters');
            $table->enum('sync', [0,1])->default(0);
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
        Schema::dropIfExists('kurikulums');
    }
};
