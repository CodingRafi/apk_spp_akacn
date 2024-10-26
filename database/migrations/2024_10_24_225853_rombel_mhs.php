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
        Schema::create('rombel_mhs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rombel_tahun_ajaran_id')->constrained('rombel_tahun_ajarans');
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
        Schema::dropIfExists('rombel_mhs');
    }
};
