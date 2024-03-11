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
        Schema::create('gaji', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_awal');
            $table->date('tgl_akhir');
            $table->enum('status', [0,1])->default(0);
            $table->timestamps();
        });
        
        Schema::create('gaji_user', function(Blueprint $table){
            $table->id();
            $table->foreignId('gaji_id')->constrained('gaji');
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('tunjangan', 20, 2);
            $table->decimal('fee_transport', 20, 2);
            $table->integer('total_kehadiran');
            $table->decimal('total_fee_transport', 20, 2);
            $table->decimal('total', 20, 2);
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
        Schema::dropIfExists('gajis');
    }
};
