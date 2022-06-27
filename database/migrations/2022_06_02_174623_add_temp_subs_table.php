<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTempSubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('temp_subs', function (Blueprint $table) {
            $table->id();
            $table->string('sub_id');
            $table->string('srv_name');
            $table->string('srv_desc')->nullable();
            $table->tinyInteger('srv_egg');
            $table->integer('user');
            $table->integer('plan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_subs');
    }
}
