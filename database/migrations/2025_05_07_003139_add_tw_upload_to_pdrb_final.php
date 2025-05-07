<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTwUploadToPdrbFinal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pdrb_final', function (Blueprint $table) {
            $table->integer("upload_tahun")->nullable();
            $table->tinyInteger("upload_q")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pdrb_final', function (Blueprint $table) {
            //
        });
    }
}
