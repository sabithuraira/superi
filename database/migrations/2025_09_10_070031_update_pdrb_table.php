<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePdrbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pdrb', function (Blueprint $table) {
            $table->dateTime('time_approve_provinsi')->nullable()->after('status_data');
            $table->dateTime('time_approve_admin')->nullable()->after('time_approve_provinsi');
            $table->dateTime('time_reject')->nullable()->after('time_approve_admin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
