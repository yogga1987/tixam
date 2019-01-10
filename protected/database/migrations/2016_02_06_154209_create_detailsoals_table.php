<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailsoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detailsoals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_soal', 150);
            $table->string('jenis', 5);
            $table->longtext('soal');
            $table->longtext('pila');
            $table->longtext('pilb');
            $table->longtext('pilc');
            $table->longtext('pild');
            $table->longtext('pile');
            $table->string('kunci', 1);
            $table->string('score', 50);
            $table->string('id_user', 15);
            $table->string('status', 1);
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
        Schema::drop('detailsoals');
    }
}
