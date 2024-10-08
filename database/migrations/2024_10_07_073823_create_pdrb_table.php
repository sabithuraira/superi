<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePdrbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdrb', function (Blueprint $table) {
            $table->increments('id');

            $table->integer("komponen_id");
            $table->integer("tahun");
            $table->tinyInteger("q");
            $table->char("kode_kab", 2);
            $table->char("kode_prov", 2);
            $table->decimal("nilai", 15,2);
            $table->tinyInteger("revisi_ke")->default(0);

            $table->tinyInteger("status_data");
            $table->integer('ketua_tim_id');
            $table->integer('pimpinan_id');
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
        Schema::dropIfExists('pdrb');
    }
}
