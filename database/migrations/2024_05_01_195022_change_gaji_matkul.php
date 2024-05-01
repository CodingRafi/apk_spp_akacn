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
        Schema::table('gaji_matkul', function (Blueprint $table) {
            $table->renameColumn('fee_sks', 'fee_sks_teori');
            $table->renameColumn('total_fee_sks', 'total_fee_sks_teori');
            $table->decimal('fee_sks_praktek', 20, 2);
            $table->decimal('total_fee_sks_praktek', 20, 2);
            $table->decimal('total', 20, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
