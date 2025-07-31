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
        Schema::table('jadwal', function (Blueprint $table) {
            $table->foreignId('ruang_id')->nullable()->constrained('ruangs');
            $table->string('tingkat')->nullable();
            $table->text('status_ujian')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropColumn('ruang_id');
            $table->dropColumn('tingkat');
            $table->dropColumn('status_ujian');
        });
    }
};
