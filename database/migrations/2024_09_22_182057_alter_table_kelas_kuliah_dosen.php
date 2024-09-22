<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kelas_kuliah_dosen', function (Blueprint $table) {
            // Hapus foreign key 'tahun_semester_id'
            $table->dropForeign(['tahun_semester_id']);
            
            // Hapus foreign key 'tahun_matkul_id'
            $table->dropForeign(['tahun_matkul_id']);
            
            // Hapus kolom 'tahun_semester_id' dan 'tahun_matkul_id'
            $table->dropColumn('tahun_semester_id');
            $table->dropColumn('tahun_matkul_id');
            
            // Tambahkan kolom 'id_kelas_kuliah'
            $table->string('id_kelas_kuliah');
        });
    }

    public function down()
    {
        Schema::table('kelas_kuliah_dosen', function (Blueprint $table) {
            // Restore kolom 'tahun_semester_id' dan foreign key-nya
            $table->unsignedBigInteger('tahun_semester_id')->nullable();
            $table->foreign('tahun_semester_id')->references('id')->on('tahun_semester');
            
            // Restore kolom 'tahun_matkul_id' dan foreign key-nya
            $table->unsignedBigInteger('tahun_matkul_id')->nullable();
            $table->foreign('tahun_matkul_id')->references('id')->on('tahun_matkul');
            
            // Hapus kolom 'id_kelas_kuliah'
            $table->dropColumn('id_kelas_kuliah');
        });
    }
};
