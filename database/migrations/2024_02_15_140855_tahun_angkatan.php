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
            $table->timestamps();
        });

        Schema::create('tahun_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_semester_id')->constrained('tahun_semester');
            $table->string('nominal');
            $table->text('ket');
            $table->enum('publish', [0,1]);
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
            $table->enum('publish', [0,1]);
            $table->timestamps();
        });

        Schema::create('tahun_kurikulum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_semester_id')->constrained('tahun_semester');
            $table->uuid('kurikulum_id');
            $table->foreign('kurikulum_id')->references('id')->on('kurikulums');
            $table->timestamps();
        });
        
        Schema::create('tahun_krs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_semester_id')->constrained('tahun_semester');
            $table->foreignId('dosen_id')->constrained('users');
            $table->uuid('matkul_id');
            $table->foreign('matkul_id')->references('id')->on('matkuls');
            $table->foreignId('ruang_id')->constrained('ruangs');
            $table->char('hari', 1);
            $table->time('jam_mulai');
            $table->time('jam_akhir');
            $table->enum('jenis', [1,2]); // 1 => online, 2 => offline
            $table->enum('cek_ip', [0,1]);
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
        Schema::dropIfExists('tahun_kurikulum');
        Schema::dropIfExists('tahun_krs');
    }
};
