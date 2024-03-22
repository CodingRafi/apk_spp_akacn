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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('id_neo_feeder')->nullable();
            $table->enum('sync_neo_feeder', [0,1])->default(0);
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
            $table->string('nama_ayah')->nullable();
            $table->string('tgl_lahir_ayah')->nullable();
            $table->string('nik_ayah')->nullable();
            $table->uuid('jenjang_ayah_id')->nullable();
            $table->foreign('jenjang_ayah_id')->references('id')->on('jenjangs');
            $table->uuid('pekerjaan_ayah_id')->nullable();
            $table->foreign('pekerjaan_ayah_id')->references('id')->on('pekerjaans');
            $table->uuid('penghasilan_ayah_id')->nullable();
            $table->foreign('penghasilan_ayah_id')->references('id')->on('penghasilans');
            $table->string('nama_ibu')->nullable();
            $table->string('tgl_lahir_ibu')->nullable();
            $table->string('nik_ibu')->nullable();
            $table->uuid('jenjang_ibu_id')->nullable();
            $table->foreign('jenjang_ibu_id')->references('id')->on('jenjangs');
            $table->uuid('pekerjaan_ibu_id')->nullable();
            $table->foreign('pekerjaan_ibu_id')->references('id')->on('pekerjaans');
            $table->uuid('penghasilan_ibu_id')->nullable();
            $table->foreign('penghasilan_ibu_id')->references('id')->on('penghasilans');
            $table->string('nama_wali')->nullable();
            $table->string('tgl_lahir_wali')->nullable();
            $table->string('nik_wali')->nullable();
            $table->uuid('jenjang_wali_id')->nullable();
            $table->foreign('jenjang_wali_id')->references('id')->on('jenjangs');
            $table->uuid('pekerjaan_wali_id')->nullable();
            $table->foreign('pekerjaan_wali_id')->references('id')->on('pekerjaans');
            $table->uuid('penghasilan_wali_id')->nullable();
            $table->foreign('penghasilan_wali_id')->references('id')->on('penghasilans');
            $table->string('telepon')->nullable();
            $table->string('handphone')->nullable();
            $table->enum('penerima_kps', [0,1])->default(0);
            $table->string('no_kps')->nullable();
            $table->string('npwp')->nullable();
            $table->uuid('agama_id');
            $table->foreign('agama_id')->references('id')->on('agamas');
            $table->foreignId('rombel_id')->constrained('rombels');
            $table->uuid('prodi_id');
            $table->foreign('prodi_id')->references('id')->on('prodi');
            $table->string('kewarganegaraan_id');
            $table->foreign('kewarganegaraan_id')->references('id')->on('kewarganegaraans');
            $table->string('wilayah_id');
            $table->foreign('wilayah_id')->references('id')->on('wilayahs');
            $table->string('jenis_tinggal_id')->nullable();
            $table->foreign('jenis_tinggal_id')->references('id')->on('jenis_tinggals');
            $table->string('alat_transportasi_id')->nullable();
            $table->foreign('alat_transportasi_id')->references('id')->on('alat_transportasis');
            $table->enum('status', [0,1])->default(1);
            $table->enum('mhs_kebutuhan_khusus', [0,1])->default(0);
            $table->enum('ayah_kebutuhan_khusus', [0,1])->default(0);
            $table->enum('ibu_kebutuhan_khusus', [0,1])->default(0);
            $table->uuid('tahun_masuk_id');
            $table->foreign('tahun_masuk_id')->references('id')->on('tahun_ajarans');
            $table->foreignId('jenis_kelas_id')->constrained('jenis_kelas');
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
