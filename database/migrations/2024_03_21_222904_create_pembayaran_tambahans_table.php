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
        Schema::create('pembayaran_tambahans', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['semester', 'lainnya']);
            $table->foreignId('mhs_id')->constrained('users');
            $table->foreignId('tahun_semester_id')->nullable()->constrained('tahun_semester');
            $table->foreignId('tahun_pembayaran_lain_id')->nullable()->constrained('tahun_pembayaran_lain');
            $table->string('nama');
            $table->decimal('nominal', 20, 2);
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
        Schema::dropIfExists('pembayaran_tambahans');
    }
};
