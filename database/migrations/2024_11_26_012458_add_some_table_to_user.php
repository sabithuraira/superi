<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeTableToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip_baru');
            $table->string('urutreog');
            $table->string('kdorg');
            $table->string('nmorg');
            $table->string('nmjab');
            $table->string('flagwil');
            $table->string('kdprop');
            $table->string('kdkab');;
            $table->string('kdkec');
            $table->string('nmwil');
            $table->string('kdgol');
            $table->string('nmgol');
            $table->string('kdstjab');
            $table->string('nmstjab');
            $table->string('kdesl');
            $table->string('foto')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
