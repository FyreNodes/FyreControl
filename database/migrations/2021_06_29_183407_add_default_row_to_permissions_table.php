<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultRowToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permissions')->insert(
            array(
                'name' => 'Owner',
                'color' => '#000000',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'p_settings' => 2,
                'p_api' => 2,
                'p_permissions' => 2,
                'p_databases' => 2,
                'p_locations' => 2,
                'p_nodes' => 2,
                'p_servers' => 2,
                'p_users' => 2,
                'p_mounts' => 2,
                'p_nests' => 2,
            )
        );
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
