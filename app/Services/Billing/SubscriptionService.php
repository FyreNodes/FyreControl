<?php

namespace Pterodactyl\Services\Billing;

use Pterodactyl\Models\Egg;
use Pterodactyl\Models\Plan;
use Pterodactyl\Models\Type;
use Pterodactyl\Models\User;
use Illuminate\Support\Facades\DB;
use Pterodactyl\Models\Subscription;
use Pterodactyl\Exceptions\DisplayException;
use Illuminate\Validation\ValidationException;
use Pterodactyl\Repositories\Eloquent\NodeRepository;
use Pterodactyl\Services\ErrorService;
use Pterodactyl\Services\Servers\ServerCreationService;
use Pterodactyl\Exceptions\Repository\RecordNotFoundException;
use Pterodactyl\Exceptions\Service\Deployment\NoViableNodeException;
use Pterodactyl\Exceptions\Service\Deployment\NoViableAllocationException;

class SubscriptionService
{
    public ErrorService $errorService;
    public Subscription $subscription;
    public ServerCreationService $serverCreationService;
    public NodeRepository $nodeRepository;

    public function __construct(Subscription $subscription, ServerCreationService $serverCreationService, NodeRepository $nodeRepository, ErrorService $errorService)
    {
        $this->errorService = $errorService;
        $this->subscription = $subscription;
        $this->serverCreationService = $serverCreationService;
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @param array $data
     * @return void
     * @throws DisplayException
     */
    public function create(array $data): void
    {
        $subscription = $this->subscription;
        if (isset($data['id'])) $subscription->sub_id = $data['id']; else $subscription->sub_id = null;
        $subscription->user = $data['user'];
        $subscription->plan = $data['plan'];
        $server_info = DB::table('temp_subs')->select(['srv_name', 'srv_desc', 'srv_egg'])->where('sub_id', '=', $subscription->sub_id)->first();
        $plan = Plan::query()->select(['cpu', 'memory', 'disk', 'swap', 'io', 'databases', 'backups', 'allocations'])->where('id', '=', $subscription->plan)->first();
        $image = Type::query()->select('default_image')->where('egg', '=', $server_info->srv_egg)->first()->default_image;
        $egg = Egg::query()->select(['id', 'nest_id', 'startup'])->where('id', '=', $server_info->srv_egg)->first();
        $srv_data = [
            'name' => $server_info->srv_name,
            'description' => $server_info->srv_desc,
            'owner_id' => $subscription->user,
            'egg_id' => $egg->id,
            'nest_id' => $egg->nest_id,
            'allocation_id' => $this->getAllocationId(['mem' => $plan->memory, 'disk' => $plan->disk]),
            'environment' => [],
            'memory' => $plan->memory,
            'disk' => $plan->disk,
            'cpu' => $plan->cpu,
            'swap' => $plan->swap,
            'io' => $plan->io,
            'allocation_limit' => $plan->allocations,
            'backup_limit' => $plan->backups,
            'database_limit' => $plan->databases,
            'image' => $image,
            'startup' => $egg->startup,
            'start_on_completion' => false,
        ];
        try {
            $server = $this->serverCreationService->handle($srv_data);
            $server->save();
            $subscription->server = $server->id;
        } catch (ValidationException|NoViableAllocationException|NoViableNodeException|DisplayException|RecordNotFoundException|\Throwable $e) {
            dd($e);
        }
        if ($plan->price === '0') {
            $user_slots = User::query()->select('slots')->where('id', '=', $subscription->user)->first()->slots;
            User::query()->where('id', '=', $subscription->user)->update(['slots' => $user_slots - 1]);
        }
        DB::table('temp_subs')->where('sub_id', '=', $subscription->sub_id)->delete();
        Subscription::query()->insert($subscription->getAttributes());
    }

    /**
     * @param array $data
     * @return int
     */
    private function getAllocationId(array $data): int
    {
        $nodes = $this->nodeRepository->getNodesForServerCreation();
        $available_nodes = [];
        foreach ($nodes as $node) {
            $x = $this->nodeRepository->getNodeWithResourceUsage($node['id']);
            if ($x->getOriginal('sum_memory') <= $x->getOriginal('memory') - $data['mem']) {
                $available_nodes[] = $x->id;
            }
        }
        if ($available_nodes > 0) {
            $node = $available_nodes[0];
        } else $this->errorService->create('CLUSTER_ERROR.NO_AVAILABLE_SPACE');
        $allocation = DB::table('allocations')->select('*')->where('node_id', '=', $node)->where('server_id', '=', null)->get()->first();
        if (!$allocation) {
            $this->errorService->create('CLUSTER_ERROR.NO_AVAILABLE_ALLOCATIONS');
        };
        return $allocation->id;
    }
}
