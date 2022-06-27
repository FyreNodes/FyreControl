<?php

namespace Pterodactyl\Http\Controllers\Api\Client\Servers;

use Pterodactyl\Models\Server;
use Spatie\Activitylog\Models\Activity;
use Pterodactyl\Http\Controllers\Api\Client\ClientApiController;
use Illuminate\Support\Facades\DB;
use Pterodactyl\Exceptions\DisplayException;
use Pterodactyl\Http\Requests\Api\Client\Servers\LogsRequest;

class LogsController extends ClientApiController
{

    public function __construct() {

    }

    public function index(LogsRequest $request, Server $server): array
    {

        $logs = DB::table('activity_log')
        ->select('id', 'description', 'created_at')
        ->where('properties', 'like', "%\"serverID\":$server->id%")
        ->orderBy('created_at', 'desc')
        ->get();

        foreach($logs as $key => $log) {
        	$activity_logs = DB::table('activity_log')->where('id', '=', $log->id)->first();
        	$logs[$key]->action = ucfirst(json_decode($activity_logs->properties)->action);
        	$logs[$key]->module = ucfirst(json_decode($activity_logs->properties)->module);
        	$logs[$key]->user = ucfirst(json_decode($activity_logs->properties)->user);
        }

        return [
            'success' => true,
            'data' => [
                'logs' => $logs,
            ],
        ];
    }

    public function delete(LogsRequest $request, Server $server, $id): array
    {
        $id = (int) $id;

        DB::table('activity_log')
        ->where('id', '=', $id)
        ->where('properties', 'like', "%\"serverID\":$server->id%")
        ->delete();

        return ['success' => true];
    }

}
