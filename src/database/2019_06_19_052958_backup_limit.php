<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BackupLimit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('backup_limit', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('server_id')->unsigned();
            $table->foreign('server_id')->references('id')->on('servers');

            $table->integer('backups')->default(3);

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
    }
}