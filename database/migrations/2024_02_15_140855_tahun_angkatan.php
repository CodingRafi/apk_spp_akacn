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
            $table->enum('status', [0, 1]);
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
            $table->string('tahun_ajaran_id');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans');
            $table->foreignId('dosen_id')->constrained('users');
            $table->uuid('kurikulum_id');
            $table->foreign('kurikulum_id')->references('id')->on('kurikulums')->onDelete('cascade');
            $table->uuid('matkul_id');
            $table->char('hari', 1);
            $table->time('jam_mulai');
            $table->time('jam_akhir');
            $table->foreign('matkul_id')->references('id')->on('matkuls');
            $table->enum('cek_ip', [0, 1]);
            $table->timestamps();
        });

        Schema::create('tahun_matkul_ruang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_matkul_id')->constrained('tahun_matkul');
            $table->foreignId('ruang_id')->constrained('ruangs');
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
            $table->foreignId('verify_id')->nullable()->constrained('users');
            $table->foreignId('tahun_semester_id')->constrained('tahun_semester');
            $table->enum('status', ['pengajuan', 'diterima', 'ditolak', 'pending'])->default('pending');
            $table->string('jml_sks_diambil')->default(0);
            $table->text('ket')->nullable();
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

        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['ujian', 'pertemuan'])->default('pertemuan');
            $table->string('kode')->unique();
            $table->foreignId('pengajar_id')->constrained('users');
            $table->timestamp('presensi_mulai')->nullable();
            $table->timestamp('presensi_selesai')->nullable();
            $table->date('tgl');
            $table->text('materi')->nullable();
            $table->string('jenis_ujian')->nullable();
            $table->foreignId('tahun_matkul_id')->constrained('tahun_matkul');
            $table->foreignId('tahun_semester_id')->constrained('tahun_semester');
            $table->text('ket')->nullable();
            $table->timestamps();
        });

        Schema::create('jadwal_presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwal');
            $table->foreignId('mhs_id')->constrained('users');
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('mhs_nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_semester_id')->constrained('tahun_semester');
            $table->foreignId('tahun_matkul_id')->constrained('tahun_matkul');
            $table->decimal('presensi', 8, 2);
            $table->decimal('uts', 8, 2);
            $table->decimal('uas', 8, 2);
            $table->foreignId('mutu_id')->constrained('mutu');
            $table->decimal('nilai_mutu', 8, 2);
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
        Schema::dropIfExists('jadwal');
        Schema::dropIfExists('jadwal_presensi');
    }
};
