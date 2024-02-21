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
        Schema::create('kuesioners', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['input', 'choice']);
            $table->text('pertanyaan');
            $table->enum('status', [0,1]);
            $table->timestamps();
        });
        
        Schema::create('t_kuesioners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mhs_id')->constrained('users');
            $table->foreignId('dosen_id')->constrained('users');
            $table->string('matkul_id');
            $table->foreign('matkul_id')->references('id')->on('matkuls');
            $table->timestamps();
        });

        Schema::create('t_kuesioners_answer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('t_kuesioner_id')->constrained('t_kuesioners');
            $table->foreignId('kuesioner_id')->constrained('kuesioners');
            $table->text('answer');
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
        Schema::dropIfExists('kuesioners');
        Schema::dropIfExists('t_kuesioners');
        Schema::dropIfExists('t_kuesioners_answer');
    }
};
