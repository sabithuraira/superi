<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTwUploadToSuperiPdrb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pdrb', function (Blueprint $table) {
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
        Schema::table('pdrb', function (Blueprint $table) {
            //
        });
    }
}
