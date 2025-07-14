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
        Schema::table('mhs_nilai', function (Blueprint $table) {
            $table->decimal('aktivitas_partisipatif', 5, 2)->nullable();
            $table->decimal('hasil_proyek', 5, 2)->nullable();
            $table->decimal('quizz', 5, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mhs_nilai', function (Blueprint $table) {
            $table->dropColumn('aktivitas_partisipatif');
            $table->dropColumn('hasil_proyek');
            $table->dropColumn('quizz');
        });
    }
};
