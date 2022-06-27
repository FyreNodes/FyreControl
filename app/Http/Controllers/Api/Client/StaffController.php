<?php

namespace Pterodactyl\Http\Controllers\Api\Client;

use Carbon\Carbon;
use Pterodactyl\Models\Server;
use Illuminate\Support\Facades\DB;
use Pterodactyl\Exceptions\DisplayException;
use Pterodactyl\Repositories\Eloquent\SubuserRepository;
use Pterodactyl\Repositories\Wings\DaemonServerRepository;
use Pterodactyl\Http\Requests\Api\Client\Account\StaffRequest;
use Pterodactyl\Exceptions\Http\Connection\DaemonConnectionException;

class StaffController extends ClientApiController
{
    /**
     * @var \Pterodactyl\Repositories\Wings\DaemonServerRepository
     */
    protected $daemonServerRepository;

    /**
     * @var \Pterodactyl\Repositories\Eloquent\SubuserRepository
     */
    protected $subuserRepository;

    /**
     * StaffController constructor.
     * @param SubuserRepository $subuserRepository
     * @param DaemonServerRepository $daemonServerRepository
     */
    public function __construct(SubuserRepository $subuserRepository, DaemonServerRepository $daemonServerRepository)
    {
        parent::__construct();

        $this->subuserRepository = $subuserRepository;
        $this->daemonServerRepository = $daemonServerRepository;
    }

    /**
     * @param StaffRequest $request
     * @return array
     */
    public function index(StaffRequest $request): array
    {
        $allRequest = DB::table('staff_requests')->get();

        foreach ($allRequest as $staffRequest) {
            if ($staffRequest->status == 2) {
                $existsSubUser = DB::table('subusers')->where('server_id', '=', $staffRequest->server_id)->where('user_id', '=', $staffRequest->staff_id)->get();
                if (count($existsSubUser) < 1) {
                    DB::table('staff_requests')->where('id', '=', $staffRequest->id)->update(['status' => 3]);
                }
            }
        }

        $servers = DB::table('servers')->select(['id', 'name', 'uuidShort'])->get();
        $requests = DB::table('staff_requests')->where('staff_id', '=', $request->user()->id)->get();

        foreach ($requests as $key => $staffRequest) {
            $requests[$key]->server = DB::table('servers')->select(['id', 'name', 'uuidShort'])->where('id', '=', $staffRequest->server_id)->first();

            if ($staffRequest->status == 1) {
                $requests[$key]->status = 'Waiting...';
            }

            if ($staffRequest->status == 2) {
                $requests[$key]->status = 'Accepted';
            }

            if ($staffRequest->status == 3) {
                $requests[$key]->status = 'Denied';
            }
        }

        return [
            'success' => true,
            'data' => [
                'servers' => $servers,
                'requests' => $requests,
            ],
        ];
    }

    /**
     * @param StaffRequest $request
     * @return array
     * @throws DisplayException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function request(StaffRequest $request): array
    {
        $this->validate($request, [
            'server' => 'required|integer',
            'message' => 'required',
        ]);

        $server_id = (int) $request->input('server', 0);
        $message = trim(strip_tags($request->input('message', '')));

        $server = DB::table('servers')->where('id', '=', $server_id)->get();
        if (count($server) < 1) {
            throw new DisplayException('Server not found.');
        }

        $existsRequest = DB::table('staff_requests')->where('server_id', '=', $server[0]->id)->where('staff_id', '=', $request->user()->id)->get();
        if (count($existsRequest) > 0) {
            throw new DisplayException('This access request is exists.');
        }

        DB::table('staff_requests')->insert([
            'staff_id' => $request->user()->id,
            'server_id' => $server[0]->id,
            'message' => $message,
            'status' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return [
            'success' => true,
            'data' => [],
        ];
    }

    /**
     * @param StaffRequest $request
     * @param $id
     * @return array
     * @throws DisplayException
     */
    public function delete(StaffRequest $request, $id): array
    {
        $id = (int) $id;

        $existsRequest = DB::table('staff_requests')->where('id', '=', $id)->where('staff_id', '=', $request->user()->id)->get();
        if (count($existsRequest) < 1) {
            throw new DisplayException('Request not found.');
        }

        $subUser = DB::table('subusers')->where('server_id', '=', $existsRequest[0]->server_id)->where('user_id', '=', $request->user()->id)->get();
        if (count($subUser) > 0) {
            try {
                $this->daemonServerRepository->setServer(Server::find($existsRequest[0]->server_id))->revokeJTIs([md5($request->user()->id . $existsRequest[0]->server_id)]);
            } catch (DaemonConnectionException $e) {
                throw new DisplayException('Failed to delete subuser from the server. Please try again later...');
            }

            $this->subuserRepository->delete($subUser[0]->id);
        }

        DB::table('staff_requests')->where('id', '=', $id)->delete();

        return [
            'success' => true,
            'data' => [],
        ];
    }
}
