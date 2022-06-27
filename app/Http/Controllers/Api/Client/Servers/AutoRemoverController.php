<?php

namespace Pterodactyl\Http\Controllers\Api\Client\Servers;

use Pterodactyl\Models\Server;
use Illuminate\Support\Facades\DB;
use Pterodactyl\Exceptions\DisplayException;
use Pterodactyl\Http\Controllers\Api\Client\ClientApiController;
use Pterodactyl\Http\Requests\Api\Client\Servers\AutoRemoverRequest;

class AutoRemoverController extends ClientApiController
{
    /**
     * @param AutoRemoverRequest $request
     * @param Server $server
     * @return array
     */
    public function index(AutoRemoverRequest $request, Server $server): array
    {
        $files = DB::table('delete_files')->where('server_id', '=', $server->id)->get();

        return [
            'success' => true,
            'data' => [
                'files' => $files,
            ],
        ];
    }

    /**
     * @param AutoRemoverRequest $request
     * @param Server $server
     * @return array
     */
    public function add(AutoRemoverRequest $request, Server $server): array
    {
        $this->validate($request, [
            'file' => 'required|min:1|max:100',
            'type' => 'required|integer|min:1|max:2',
            'day' => 'required',
            'hour' => 'required|integer|min:0|max:23',
            'minute' => 'required|integer|min:0|max:59',
        ]);

        $day = trim(strip_tags($request->input('day', '*')));

        if ($day != '*') {
            if ((int) $day < 1 || (int) $day > 7) {
                throw new DisplayException('Invalid day.');
            }

            $type = (int) $day . '|' . (int) $request->input('hour', 0) . ':' . (int) $request->input('minute', 0);
        } else {
            $type = '*|' . (int) $request->input('hour', 0) . ':' . (int) $request->input('minute', 0);
        }

        $user = $request->user();
        activity()
            ->causedBy($user)
            ->performedOn($server)
            ->withProperties([
                'serverID' => $server->id,
                'module' => 'Cleanup',
                'action' => 'Created',
                'user' => $user->name_first
            ])
            ->log('A cleanup was created for '.$request->input('file'));

        DB::table('delete_files')->insert([
            'server_id' => $server->id,
            'file' => trim(strip_tags($request->input('file', ''))),
            'type' => $type,
        ]);

        return [
            'success' => true,
            'data' => [],
        ];
    }

    /**
     * @param AutoRemoverRequest $request
     * @param Server $server
     * @param $id
     * @return array
     * @throws 270 DisplayException
     */
    public function remove(AutoRemoverRequest $request, Server $server, $id): array
    {
        $id = (int) $id;

        $exists = DB::table('delete_files')->where('id', '=', $id)->where('server_id', '=', $server->id)->get();
        if (count($exists) < 1) {
            throw new DisplayException('File not found.');
        }

        $user = $request->user();
        activity()
            ->causedBy($user)
            ->performedOn($server)
            ->withProperties([
                'serverID' => $server->id,
                'module' => 'Cleanup',
                'action' => 'Deleted',
                'user' => $user->name_first
            ])
            ->log('The cleanup for '.$request->input('file').' was deleted.');

        DB::table('delete_files')->where('id', '=', $id)->where('server_id', '=', $server->id)->delete();

        return [
            'success' => true,
            'data' => [],
        ];
    }
}
