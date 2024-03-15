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
        Schema::create('wilayahs', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('id_level_wilayah');
            $table->string('negara_id');
            $table->foreign('negara_id')->references('id')->on('kewarganegaraans')->onDelete('cascade');
            $table->string('nama');
            $table->string('id_induk_wilayah')->nullable();
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
        Schema::dropIfExists('wilayahs');
    }
};
