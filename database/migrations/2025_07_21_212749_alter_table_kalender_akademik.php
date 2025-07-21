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
        Schema::table('kalender_akademiks', function (Blueprint $table) {
            $table->dropColumn('start_time');
            $table->dropColumn('finish_time');
            $table->dropColumn('comments');

            $table->string('nama');
            $table->boolean('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kalender_akademiks', function (Blueprint $table) {
            $table->datetime('start_time');
            $table->datetime('finish_time');
            $table->longText('comments')->nullable();

            $table->dropColumn('nama');
            $table->dropColumn('is_active');
        });
    }
};
