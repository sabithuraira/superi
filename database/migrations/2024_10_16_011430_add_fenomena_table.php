<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFenomenaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fenomena', function (Blueprint $table) {
            $table->increments('id');

            // $table->string("komponen_id", 5);
            $table->integer("tahun");
            $table->tinyInteger("q");
            $table->char("kode_kab", 2);
            $table->char("kode_prov", 2);

            $table->string("pertumbuhan");

            $table->text("fenomena_c_1", 15, 2);
            $table->text("fenomena_c_1a", 15, 2);
            $table->text("fenomena_c_1b", 15, 2);
            $table->text("fenomena_c_1c", 15, 2);
            $table->text("fenomena_c_1d", 15, 2);
            $table->text("fenomena_c_1e", 15, 2);
            $table->text("fenomena_c_1f", 15, 2);
            $table->text("fenomena_c_1g", 15, 2);
            $table->text("fenomena_c_1h", 15, 2);
            $table->text("fenomena_c_1i", 15, 2);
            $table->text("fenomena_c_1j", 15, 2);
            $table->text("fenomena_c_1k", 15, 2);
            $table->text("fenomena_c_1l", 15, 2);
            $table->text("fenomena_c_2", 15, 2);
            $table->text("fenomena_c_3", 15, 2);
            $table->text("fenomena_c_3a", 15, 2);
            $table->text("fenomena_c_3b", 15, 2);
            $table->text("fenomena_c_4", 15, 2);
            $table->text("fenomena_c_4a", 15, 2);
            $table->text("fenomena_c_4b", 15, 2);
            $table->text("fenomena_c_5", 15, 2);
            $table->text("fenomena_c_6", 15, 2);
            $table->text("fenomena_c_6a", 15, 2);
            $table->text("fenomena_c_6b", 15, 2);
            $table->text("fenomena_c_7", 15, 2);
            $table->text("fenomena_c_7a", 15, 2);
            $table->text("fenomena_c_7b", 15, 2);
            $table->text("fenomena_c_8", 15, 2);
            $table->text("fenomena_c_8a", 15, 2);
            $table->text("fenomena_c_8b", 15, 2);
            $table->text("fenomena_c_pdrb", 15, 2);

            $table->integer('created_by');
            $table->integer('updated_by');
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
        //
        Schema::dropIfExists('fenomena');
    }
}
