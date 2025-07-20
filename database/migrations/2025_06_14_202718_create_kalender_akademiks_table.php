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
        Schema::create('kalender_akademiks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->datetime('start_time');
            $table->datetime('finish_time');
            $table->longText('comments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kalender_akademiks');
    }
};
