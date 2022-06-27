<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
             $table->id();
             $table->string('stripe_id')->nullable();
             $table->string('price_id')->nullable();
             $table->string('name');
             $table->string('description', 500000);
             $table->string('image');
             $table->string('price');
             $table->smallInteger('cpu');
             $table->smallInteger('memory');
             $table->integer('disk');
             $table->smallInteger('swap');
             $table->smallInteger('io');
             $table->tinyInteger('databases');
             $table->tinyInteger('allocations');
             $table->tinyInteger('backups');
             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
}
