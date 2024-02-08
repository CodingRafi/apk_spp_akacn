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
        Schema::create('profile_pengajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('tempat_lahir');
            $table->date('tgl_lahir');
            $table->string('no_telp');
            $table->uuid('agama_id');
            $table->foreign('agama_id')->references('id')->on('agamas')->onDelete('cascade');
            $table->enum('status', [0,1]);
            $table->string('nidn');
            $table->string('nama_ibu');
            $table->string('nik');
            $table->string('npwp');
            $table->string('no_sk_cpns')->nullable();
            $table->string('tgl_sk_cpns')->nullable();
            $table->string('no_sk_pengangkatan')->nullable();
            $table->string('mulai_sk_pengangkatan')->nullable();
            $table->enum('jk', ['l', 'p']);
            $table->string('riwayat_pendidikan');
            $table->text('alamat');
            $table->string('nominal_tunjangan')->default('0');
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
        Schema::dropIfExists('profile_pengajars');
    }
};
