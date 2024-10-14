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
            
            $table->decimal("c_1", 15,2);
            $table->decimal("c_1a", 15,2);
            $table->decimal("c_1b", 15,2);
            $table->decimal("c_1c", 15,2);
            $table->decimal("c_1d", 15,2);
            $table->decimal("c_1e", 15,2);
            $table->decimal("c_1f", 15,2);
            $table->decimal("c_1g", 15,2);
            $table->decimal("c_1h", 15,2);
            $table->decimal("c_1i", 15,2);
            $table->decimal("c_1j", 15,2);
            $table->decimal("c_1k", 15,2);
            $table->decimal("c_1l", 15,2);
            $table->decimal("c_2", 15,2);
            $table->decimal("c_3", 15,2);
            $table->decimal("c_3a", 15,2);
            $table->decimal("c_3b", 15,2);
            $table->decimal("c_4", 15,2);
            $table->decimal("c_4a", 15,2);
            $table->decimal("c_4b", 15,2);
            $table->decimal("c_5", 15,2);
            $table->decimal("c_6", 15,2);
            $table->decimal("c_6a", 15,2);
            $table->decimal("c_6b", 15,2);
            $table->decimal("c_7", 15,2);
            $table->decimal("c_7a", 15,2);
            $table->decimal("c_7b", 15,2);
            $table->decimal("c_8", 15,2);
            $table->decimal("c_8a", 15,2);
            $table->decimal("c_8b", 15,2);
            $table->decimal("c_pdrb", 15,2);

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
