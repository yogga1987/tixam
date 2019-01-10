<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('soals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_user', 50);
            $table->char('jenis', 1);
            $table->integer('materi');
            $table->string('paket', 255);
            $table->string('deskripsi', 255);
            $table->string('kkm', 5);
            $table->string('waktu', 25);
            $table->char('tampil', 1);
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
        Schema::drop('soals');
    }
}
