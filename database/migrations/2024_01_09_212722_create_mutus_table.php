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
        Schema::create('mutu', function (Blueprint $table) {
            $table->id();
            $table->uuid('prodi_id');
            $table->foreign('prodi_id')->references('id')->on('prodi');
            $table->char('nama');
            $table->decimal('nilai', 5, 2);
            $table->decimal('bobot_minimum', 5, 2);
            $table->decimal('bobot_maksimum', 5, 2);
            $table->enum('status', [0, 1]);
            $table->date('tanggal_mulai_efektif');
            $table->date('tanggal_akhir_efektif');
            $table->string('id_neo_feeder')->nullable();
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
        Schema::dropIfExists('mutus');
    }
};
