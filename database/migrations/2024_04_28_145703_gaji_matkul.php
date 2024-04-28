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
        Schema::create('gaji_matkul', function(Blueprint $table){
            $table->id();
            $table->foreignId('gaji_id')->constrained('gaji');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('tahun_matkul_id')->constrained('tahun_matkul');
            $table->integer('total_kehadiran');
            $table->integer('sks');
            $table->decimal('fee_sks', 20, 2);
            $table->decimal('total_fee_sks', 20, 2);
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
        Schema::dropIfExists('gaji_matkul');
    }
};
