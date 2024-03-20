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
        Schema::create('biaya_lainnyas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('biaya_lainnya_tahun', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_lainnya_id')->contrained('biaya_lainnyas');
            $table->uuid('tahun_ajaran_id');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans') ;
            $table->uuid('semester_id');
            $table->foreign('semester_id')->references('id')->on('semesters') ;
            $table->string('nominal');
            $table->text('ket');
            $table->enum('publish', [0,1]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('biaya_lainnyas');
        Schema::dropIfExists('biaya_lainnya_tahun');
    }
};
