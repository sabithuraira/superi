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

            // $table->string("komponen_id", 5);
            $table->integer("tahun");
            $table->tinyInteger("q");
            $table->char("kode_kab", 2);
            $table->char("kode_prov", 2);
            $table->tinyInteger("revisi_ke")->default(0);
            
            $table->decimal("c_1", 22,8);
            $table->decimal("c_1a", 22,8);
            $table->decimal("c_1b", 22,8);
            $table->decimal("c_1c", 22,8);
            $table->decimal("c_1d", 22,8);
            $table->decimal("c_1e", 22,8);
            $table->decimal("c_1f", 22,8);
            $table->decimal("c_1g", 22,8);
            $table->decimal("c_1h", 22,8);
            $table->decimal("c_1i", 22,8);
            $table->decimal("c_1j", 22,8);
            $table->decimal("c_1k", 22,8);
            $table->decimal("c_1l", 22,8);
            $table->decimal("c_2", 22,8);
            $table->decimal("c_3", 22,8);
            $table->decimal("c_3a", 22,8);
            $table->decimal("c_3b", 22,8);
            $table->decimal("c_4", 22,8);
            $table->decimal("c_4a", 22,8);
            $table->decimal("c_4b", 22,8);
            $table->decimal("c_5", 22,8);
            $table->decimal("c_6", 22,8);
            $table->decimal("c_6a", 22,8);
            $table->decimal("c_6b", 22,8);
            $table->decimal("c_7", 22,8);
            $table->decimal("c_7a", 22,8);
            $table->decimal("c_7b", 22,8);
            $table->decimal("c_8", 22,8);
            $table->decimal("c_8a", 22,8);
            $table->decimal("c_8b", 22,8);
            $table->decimal("c_pdrb", 22,8);

            $table->tinyInteger("adhb_or_adhk");
            $table->tinyInteger("status_data");
            $table->integer('ketua_tim_id')->nullable();
            $table->integer('pimpinan_id')->nullable();
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
