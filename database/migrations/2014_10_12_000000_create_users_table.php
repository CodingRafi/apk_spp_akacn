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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('id_neo_feeder')->nullable();
            $table->string('name');
            $table->string('profile')->nullable();
            $table->string('login_key')->unique();
            $table->string('email')->nullable();
            $table->string('password')->default('$2a$12$Y77kytVxQz.p3URQJsXCVeemiziAWyLSK./icljtvK.6FyEB/2i1K');
            $table->string('ttd')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
