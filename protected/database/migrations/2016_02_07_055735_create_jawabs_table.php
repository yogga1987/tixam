<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJawabsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jawabs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no_soal_id', 15);
            $table->string('id_soal', 150);
            $table->string('id_user', 15);
            $table->string('pilihan', 1);
            $table->string('score', 10);
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
        Schema::drop('jawabs');
    }
}
