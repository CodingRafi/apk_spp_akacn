<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kelas_kuliah', function (Blueprint $table) {
            // Hapus foreign key constraint
            $table->dropForeign(['tahun_semester_id']);
            
            // Hapus kolom tahun_semester_id
            $table->dropColumn('tahun_semester_id');
        });
    }

    public function down()
    {
        Schema::table('kelas_kuliah', function (Blueprint $table) {
            // Restore kolom tahun_semester_id
            $table->unsignedBigInteger('tahun_semester_id')->nullable();

            // Restore foreign key constraint
            $table->foreign('tahun_semester_id')->references('id')->on('tahun_semester');
        });
    }
};
