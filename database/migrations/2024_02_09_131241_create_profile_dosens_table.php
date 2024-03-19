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
        Schema::create('profile_dosens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('tempat_lahir');
            $table->date('tgl_lahir');
            $table->enum('jk', ['l', 'p']);
            $table->uuid('agama_id');
            $table->foreign('agama_id')->references('id')->on('agamas')->onDelete('cascade');
            $table->enum('status', [0, 1]);
            $table->string('nip')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('nik')->nullable();
            $table->string('npwp')->nullable();
            $table->string('no_sk_cpns')->nullable();
            $table->date('tgl_sk_cpns')->nullable();
            $table->string('no_sk_pengangkatan')->nullable();
            $table->date('mulai_sk_pengangkatan')->nullable();
            $table->string('lembaga_pengangkat_id')->nullable();
            $table->foreign('lembaga_pengangkat_id')->references('id')->on('lembaga_pengangkats')->onDelete('cascade');
            $table->string('nama_pangkat_golongan')->nullable();
            $table->string('jalan')->nullable();
            $table->string('dusun')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('kewarganegaraan_id');
            $table->foreign('kewarganegaraan_id')->references('id')->on('kewarganegaraans')->onDelete('cascade');
            $table->uuid('wilayah_id')->nullable();
            $table->foreign('wilayah_id')->references('id')->on('wilayahs')->onDelete('cascade');
            $table->string('telepon')->nullable();
            $table->string('handphone')->nullable();
            $table->string('status_pernikahan')->nullable();
            $table->date('tgl_mulai_pns')->nullable();
            $table->enum('mampu_handle_kebutuhan_khusus',[0,1])->nullable();
            $table->enum('mampu_handle_kebutuhan_braille',[0,1])->nullable();
            $table->enum('mampu_handle_kebutuhan_bahasa_isyarat',[0,1])->nullable();
            $table->string('nominal_tunjangan')->default('0');
            $table->enum('source', ['neo_feeder', 'app'])->default('app');
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
        Schema::dropIfExists('profile_dosens');
    }
};
