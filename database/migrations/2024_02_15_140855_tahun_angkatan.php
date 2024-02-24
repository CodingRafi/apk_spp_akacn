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
        Schema::create('tahun_semester', function (Blueprint $table) {
            $table->id();
            $table->uuid('prodi_id');
            $table->foreign('prodi_id')->references('id')->on('prodi');
            $table->string('tahun_ajaran_id');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans');
            $table->string('semester_id');
            $table->foreign('semester_id')->references('id')->on('semesters');
            $table->string('jatah_sks');
            $table->timestamps();
        });

        Schema::create('tahun_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_semester_id')->constrained('tahun_semester');
            $table->string('nominal');
            $table->text('ket');
            $table->enum('publish', [0, 1]);
            $table->timestamps();
        });

        Schema::create('tahun_pembayaran_lain', function (Blueprint $table) {
            $table->id();
            $table->uuid('prodi_id');
            $table->foreign('prodi_id')->references('id')->on('prodi');
            $table->string('tahun_ajaran_id');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans');
            $table->foreignId('pembayaran_lainnya_id')->constrained('pembayaran_lainnyas');
            $table->string('nominal');
            $table->text('ket');
            $table->enum('publish', [0, 1]);
            $table->timestamps();
        });

        Schema::create('tahun_matkul', function (Blueprint $table) {
            $table->id();
            $table->uuid('prodi_id');
            $table->foreign('prodi_id')->references('id')->on('prodi');
            $table->string('tahun_ajaran_id');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans');
            $table->foreignId('dosen_id')->constrained('users');
            $table->uuid('matkul_id');
            $table->foreign('matkul_id')->references('id')->on('matkuls');
            $table->foreignId('ruang_id')->constrained('ruangs');
            $table->char('hari', 1);
            $table->time('jam_mulai');
            $table->time('jam_akhir');
            $table->enum('cek_ip', [0, 1]);
            $table->timestamps();
        });

        Schema::create('tahun_matkul_rombel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_matkul_id')->constrained('tahun_matkul');
            $table->foreignId('rombel_id')->constrained('rombels');
            $table->timestamps();
        });

        Schema::create('krs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mhs_id')->constrained('users');
            $table->foreignId('verify_id')->constrained('users');
            $table->uuid('prodi_id');
            $table->foreign('prodi_id')->references('id')->on('prodi');
            $table->string('tahun_ajaran_id');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans');
            $table->string('semester_id');
            $table->foreign('semester_id')->references('id')->on('semesters');
            $table->enum('status', ['pengajuan', 'diterima', 'ditolak'])->default('pengajuan');
            $table->text('ket');
            $table->date('tgl_mulai_revisi')->nullable();
            $table->date('tgl_akhir_revisi')->nullable();
            $table->timestamps();
        });

        Schema::create('krs_matkul', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_id')->constrained('krs');
            $table->foreignId('tahun_matkul_id')->constrained('tahun_matkul');
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
        Schema::dropIfExists('tahun_semester');
        Schema::dropIfExists('tahun_pembayaran');
        Schema::dropIfExists('tahun_pembayaran_lain');
        Schema::dropIfExists('tahun_matkul');
        Schema::dropIfExists('tahun_matkul_rombel');
        Schema::dropIfExists('krs');
        Schema::dropIfExists('krs_matkul');
    }
};
