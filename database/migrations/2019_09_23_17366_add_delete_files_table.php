<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeleteFilesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('delete_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('server_id');
            $table->text('file');
            $table->text('type');
            $table->date('last_deleted')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('delete_files');
    }
}
