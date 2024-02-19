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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mhs_id')->constrained('users');
            $table->foreignId('verify_id')->nullable()->constrained('users');
            $table->foreignId('tahun_semester_id')->nullable()->constrained('tahun_semester');
            $table->foreignId('tahun_pembayaran_lain_id')->nullable()->constrained('tahun_pembayaran_lain');
            $table->date('tgl_bayar');
            $table->string('nominal');
            $table->string('bukti');
            $table->enum('status', ['pengajuan','diterima', 'ditolak']);
            $table->enum('revisi', ['0', '1'])->default('0');
            $table->string('ket_mhs')->nullable();
            $table->string('ket_verify')->nullable();
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
        Schema::dropIfExists('pembayarans');
    }
};
