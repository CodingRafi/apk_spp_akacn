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
        Schema::create('profile_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->string('nisn');
            $table->string('nik');
            $table->string('tempat_lahir');
            $table->date('tgl_lahir');
            $table->enum('jk', ['l', 'p']);
            $table->string('jalan');
            $table->string('rt');
            $table->string('rw');
            $table->string('dusun');
            $table->string('kelurahan');
            $table->string('kode_pos');
            $table->string('nama_ayah');
            $table->string('tgl_lahir_ayah');
            $table->string('nik_ayah');
            $table->uuid('jenjang_ayah_id');
            $table->foreign('jenjang_ayah_id')->references('id')->on('jenjangs')->onDelete('cascade');
            $table->uuid('pekerjaan_ayah_id');
            $table->foreign('pekerjaan_ayah_id')->references('id')->on('pekerjaans')->onDelete('cascade');
            $table->uuid('penghasilan_ayah_id');
            $table->foreign('penghasilan_ayah_id')->references('id')->on('penghasilans')->onDelete('cascade');
            $table->string('nama_ibu');
            $table->string('tgl_lahir_ibu');
            $table->string('nik_ibu');
            $table->uuid('jenjang_ibu_id');
            $table->foreign('jenjang_ibu_id')->references('id')->on('jenjangs')->onDelete('cascade');
            $table->uuid('pekerjaan_ibu_id');
            $table->foreign('pekerjaan_ibu_id')->references('id')->on('pekerjaans')->onDelete('cascade');
            $table->uuid('penghasilan_ibu_id');
            $table->foreign('penghasilan_ibu_id')->references('id')->on('penghasilans')->onDelete('cascade');
            $table->string('nama_wali');
            $table->string('tgl_lahir_wali');
            $table->string('nik_wali');
            $table->uuid('jenjang_wali_id');
            $table->foreign('jenjang_wali_id')->references('id')->on('jenjangs')->onDelete('cascade');
            $table->uuid('pekerjaan_wali_id');
            $table->foreign('pekerjaan_wali_id')->references('id')->on('pekerjaans')->onDelete('cascade');
            $table->uuid('penghasilan_wali_id');
            $table->foreign('penghasilan_wali_id')->references('id')->on('penghasilans')->onDelete('cascade');
            $table->string('telepon')->nullable();
            $table->string('handphone')->nullable();
            $table->enum('penerima_kps', [0,1]);
            $table->string('no_kps');
            $table->string('npwp')->nullable();
            $table->uuid('rombel');
            $table->foreign('rombel')->references('id')->on('rombels')->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->uuid('prodi_id');
            $table->foreign('prodi_id')->references('id')->on('prodi')->onDelete('cascade');
            $table->enum('status', [0,1])->default(1);
            $table->uuid('tahun_masuk_id');
            $table->foreign('tahun_masuk_id')->references('id')->on('tahun_ajarans')->onDelete('cascade');
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
        Schema::dropIfExists('profile_mahasiswas');
    }
};
