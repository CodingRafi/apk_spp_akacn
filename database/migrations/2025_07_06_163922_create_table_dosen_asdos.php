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
        Schema::create('dosen_asdos', function (Blueprint $table) {
            $table->unsignedBigInteger('dosen_id');
            $table->unsignedBigInteger('asdos_id');
            $table->timestamp('created_at')->useCurrent();
            
            // Primary key gabungan
            $table->primary(['dosen_id', 'asdos_id']);

            // Foreign key ke tabel users
            $table->foreign('dosen_id')->references('id')->on('users');
            $table->foreign('asdos_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dosen_asdos');
    }
};
