<?php

namespace Pterodactyl\Http\Controllers\Api\Client\Servers;

use Carbon\Carbon;
use Pterodactyl\Models\Server;
use Pterodactyl\Models\Permission;
use Illuminate\Support\Facades\DB;
use Pterodactyl\Exceptions\DisplayException;
use Pterodactyl\Repositories\Eloquent\SubuserRepository;
use Pterodactyl\Exceptions\Model\DataValidationException;
use Pterodactyl\Services\Subusers\SubuserCreationService;
use Pterodactyl\Repositories\Wings\DaemonServerRepository;
use Pterodactyl\Http\Requests\Api\Client\Servers\StaffRequest;
use Pterodactyl\Http\Controllers\Api\Client\ClientApiController;
use Pterodactyl\Exceptions\Http\Connection\DaemonConnectionException;
use Pterodactyl\Exceptions\Service\Subuser\UserIsServerOwnerException;
use Pterodactyl\Exceptions\Service\Subuser\ServerSubuserExistsException;

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
     * @var \Pterodactyl\Services\Subusers\SubuserCreationService
     */
    protected $subuserCreationService;

    /**
     * StaffController constructor.
     * @param SubuserRepository $subuserRepository
     * @param SubuserCreationService $subuserCreationService
     * @param DaemonServerRepository $daemonServerRepository
     */
    public function __construct(SubuserRepository $subuserRepository, SubuserCreationService $subuserCreationService, DaemonServerRepository $daemonServerRepository)
    {
        parent::__construct();

        $this->subuserRepository = $subuserRepository;
        $this->subuserCreationService = $subuserCreationService;
        $this->daemonServerRepository = $daemonServerRepository;
    }

    /**
     * @param StaffRequest $request
     * @param Server $server
     * @return array
     */
    public function index(StaffRequest $request, Server $server): array
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

        $requests = DB::table('staff_requests')->where('server_id', '=', $server->id)->get();

        foreach ($requests as $key => $staffRequest) {
            $requests[$key]->server = DB::table('servers')->select(['id', 'name', 'uuidShort'])->where('id', '=', $staffRequest->server_id)->first();
            $requests[$key]->user = DB::table('users')->select(['id', 'name_first', 'name_last'])->where('id', '=', $staffRequest->staff_id)->first();

            $requests[$key]->status_code = $staffRequest->status;

            if ($staffRequest->status == 1) {
                $requests[$key]->status = 'Waiting...';
            }

            if ($staffRequest->status == 2) {
                $requests[$key]->status = 'Accepted';
            }

            if ($staffRequest->status == 3) {
                $requests[$key]->status = 'Denied';
            }

            unset($requests[$key]->staff_id);
        }

        return [
            'success' => true,
            'data' => [
                'requests' => $requests,
            ],
        ];
    }

    /**
     * @param StaffRequest $request
     * @param Server $server
     * @param $id
     * @return array
     * @throws DisplayException
     */
    public function accept(StaffRequest $request, Server $server, $id): array
    {
        $id = (int) $id;

        $existsRequest = DB::table('staff_requests')->where('id', '=', $id)->where('server_id', '=', $server->id)->get();
        if (count($existsRequest) < 1) {
            throw new DisplayException('Server not found.');
        }

        if ($existsRequest[0]->status == 2) {
            throw new DisplayException('You have already accepted this request.');
        }

        $staff = DB::table('users')->select(['id', 'email'])->where('id', '=', $existsRequest[0]->staff_id)->get();
        if (count($staff) < 1) {
            throw new DisplayException('Staff not found.');
        }

        $permissions = [];
        foreach (Permission::permissions()->all() as $key => $perm) {
            if (in_array($key, ['staff'])){
                continue;
            }

            foreach ($perm['keys'] as $subKey => $sub) {
                if (in_array($key . '.' . $subKey, ['user.create', 'user.update', 'user.delete'])){
                    continue;
                }

                array_push($permissions, $key . '.' . $subKey);
            }
        }

        try {
            $this->subuserCreationService->handle($server, $staff[0]->email, $permissions);
        } catch (DataValidationException $e) {
            throw new DisplayException('Failed to add subuser to the server.');
        } catch (ServerSubuserExistsException $e) {
            throw new DisplayException('Staff already has access to the server.');
        } catch (UserIsServerOwnerException $e) {
            throw new DisplayException('Failed to add subuser to the server.');
        } catch (\Throwable $e) {
            throw new DisplayException('Failed to add subuser to the server.');
        }

        DB::table('staff_requests')->where('id', '=', $existsRequest[0]->id)->update([
            'status' => 2,
            'updated_at' => Carbon::now(),
        ]);

        $user = $request->user();
        activity()
            ->causedBy($user)
            ->performedOn($server)
            ->withProperties([
                'serverID' => $server->id,
                'module' => 'Support',
                'action' => 'Granted',
                'user' => $user->name_first
            ])
            ->log($staff[0]->name_first.' was granted access.');

        return [
            'success' => true,
            'data' => [],
        ];
    }

    /**
     * @param StaffRequest $request
     * @param Server $server
     * @param $id
     * @return array
     * @throws DisplayException
     */
    public function deny(StaffRequest $request, Server $server, $id): array
    {
        $id = (int) $id;

        $existsRequest = DB::table('staff_requests')->where('id', '=', $id)->where('server_id', '=', $server->id)->get();
        if (count($existsRequest) < 1) {
            throw new DisplayException('Server not found.');
        }

        $staff = DB::table('users')->select(['id', 'email'])->where('id', '=', $existsRequest[0]->staff_id)->get();
        if (count($staff) < 1) {
            throw new DisplayException('Staff not found.');
        }

        if ($existsRequest[0]->status == 3) {
            throw new DisplayException('You have already denied this request.');
        }

        $subUser = DB::table('subusers')->where('server_id', '=', $server->id)->where('user_id', '=', $existsRequest[0]->staff_id)->get();
        if (count($subUser) > 0) {
            try {
                $this->daemonServerRepository->setServer($server)->revokeJTIs([md5($existsRequest[0]->staff_id . $server->id)]);
            } catch (DaemonConnectionException $e) {
                throw new DisplayException('Failed to delete subuser from the server. Please try again later...');
            }

            $this->subuserRepository->delete($subUser[0]->id);
        }

        DB::table('staff_requests')->where('id', '=', $existsRequest[0]->id)->update([
            'status' => 3,
            'updated_at' => Carbon::now(),
        ]);

        $user = $request->user();
        activity()
            ->causedBy($user)
            ->performedOn($server)
            ->withProperties([
                'serverID' => $server->id,
                'module' => 'Support',
                'action' => 'Denied',
                'user' => $user->name_first
            ])
            ->log($staff[0]->name_first.' was denied access.');

        return [
            'success' => true,
            'data' => [],
        ];
    }
}
