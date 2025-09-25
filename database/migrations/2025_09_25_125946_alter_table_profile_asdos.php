<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
       Schema::table('profile_asdos', function () {
            DB::statement('ALTER TABLE profile_asdos MODIFY tempat_lahir VARCHAR(255) NULL');
            DB::statement('ALTER TABLE profile_asdos MODIFY tgl_lahir DATE NULL');
            DB::statement('ALTER TABLE profile_asdos MODIFY agama_id CHAR(36) NULL');
            DB::statement('ALTER TABLE profile_asdos MODIFY kewarganegaraan_id VARCHAR(255) NULL');
            DB::statement('ALTER TABLE profile_asdos MODIFY wilayah_id CHAR(36) NULL');
            DB::statement("ALTER TABLE profile_asdos MODIFY jk ENUM('l','p') NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profile_asdos', function () {
            DB::statement('ALTER TABLE profile_asdos MODIFY tempat_lahir VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE profile_asdos MODIFY tgl_lahir DATE NOT NULL');
            DB::statement('ALTER TABLE profile_asdos MODIFY agama_id CHAR(36) NOT NULL');
            DB::statement('ALTER TABLE profile_asdos MODIFY kewarganegaraan_id VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE profile_asdos MODIFY wilayah_id CHAR(36) NOT NULL');
            DB::statement("ALTER TABLE profile_asdos MODIFY jk ENUM('l','p') NOT NULL");
        });
    }
};
