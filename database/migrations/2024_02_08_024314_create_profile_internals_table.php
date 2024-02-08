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
        Schema::create('profile_internals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('tempat_lahir');
            $table->string('nip');
            $table->date('tgl_lahir');
            $table->string('no_telp');
            $table->enum('jk', ['l', 'p']);
            $table->string('riwayat_pendidikan');
            $table->text('alamat');
            $table->string('status');
            $table->string('nominal_tunjangan')->default('0');
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
        Schema::dropIfExists('profile_internals');
    }
};
