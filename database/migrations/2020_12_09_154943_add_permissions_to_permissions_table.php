<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionsToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->unsignedTinyInteger('p_settings')->default(0);
            $table->unsignedTinyInteger('p_api')->default(0);
            $table->unsignedTinyInteger('p_permissions')->default(0);
            $table->unsignedTinyInteger('p_databases')->default(0);
            $table->unsignedTinyInteger('p_locations')->default(0);
            $table->unsignedTinyInteger('p_nodes')->default(0);
            $table->unsignedTinyInteger('p_servers')->default(0);
            $table->unsignedTinyInteger('p_users')->default(0);
            $table->unsignedTinyInteger('p_mounts')->default(0);
            $table->unsignedTinyInteger('p_nests')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            //
        });
    }
}
