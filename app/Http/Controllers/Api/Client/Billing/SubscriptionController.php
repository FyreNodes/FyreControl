<?php

namespace Pterodactyl\Http\Controllers\Api\Client\Billing;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Pterodactyl\Exceptions\DisplayException;
use Pterodactyl\Http\Controllers\Api\Client\ClientApiController;
use Pterodactyl\Models\Plan;
use Pterodactyl\Repositories\Eloquent\NodeRepository;
use Pterodactyl\Services\Billing\SubscriptionService;
use Pterodactyl\Services\ErrorService;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class SubscriptionController extends ClientApiController
{
    public NodeRepository $nodeRepository;
    public ErrorService $errorService;
    public SubscriptionService $subscriptionService;

    public function __construct(ErrorService $errorService, NodeRepository $nodeRepository, SubscriptionService $subscriptionService)
    {
        parent::__construct();
        $this->errorService = $errorService;
        $this->nodeRepository = $nodeRepository;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @throws DisplayException
     * @throws ApiErrorException
     */
    public function subscribe(Request $request): JsonResponse
    {
        $server_mem = Plan::query()->where('id', '=', $request->input('id'))->first()->memory;
        $nodes = $this->nodeRepository->getNodesForServerCreation();
        $available_nodes = array();
        foreach ($nodes as $node) {
            $x = $this->nodeRepository->getNodeWithResourceUsage($node['id']);
            if ($x->getOriginal('sum_memory') <= $x->getOriginal('memory') - $server_mem) {
                $available_nodes[] = $x->id;
            }
        }
        if (!isset($available_nodes[0])) throw new DisplayException('There are no available clusters for instance deployment.');
        $plan = Plan::query()->select(['stripe_id', 'price'])->where('id', '=', $request->input('id'))->first();
        if ($plan->price === '0') {
            if ($request->user()->slots < 1) throw new DisplayException('You do not have an available slot for this instance.');
            $subscription_id = $this->genString(12);
            $data = [
                'id' => $subscription_id,
                'plan' => $request->input('id'),
                'user' => $request->user()->id
            ];
            DB::table('temp_subs')->insert(['sub_id' => $subscription_id, 'srv_name' => $request->input('name'), 'srv_desc' => $request->input('description', null), 'srv_egg' => $request->input('egg'), 'user' => $request->user()->id, 'plan' => $request->input('id')]);
            $this->subscriptionService->create($data);
            $url = '/';
        } else {
            $client = new StripeClient(env('STRIPE_CLIENT_SECRET'));
            $prod = $client->products->retrieve($plan->stripe_id);
            $checkout = $client->checkout->sessions->create([
                'success_url' => 'https://control.fyrenodes.com/api/client/billing/subscriptions/callback/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'https://control.fyrenodes.com/api/client/billing/subscriptions/callback/fail',
                'mode' => 'subscription',
                'customer_email' => $request->user()->email,
                'line_items' => [
                    [
                        'price' => $prod->default_price,
                        'quantity' => 1
                    ]
                ]
            ]);
            DB::table('temp_subs')->insert(['sub_id' => $checkout->id, 'srv_name' => $request->input('name'), 'srv_desc' => $request->input('description', null), 'srv_egg' => $request->input('egg'), 'user' => $request->user()->id, 'plan' => $request->input('id')]);
            $url = $checkout->url;
        }
        return new JsonResponse($url, 200, [], null, true);
    }

    /**
     * @return RedirectResponse
     */
    public function success(): RedirectResponse
    {
        return redirect()->to('/');
    }

    public function genString(int $length): string
    {
        $chars = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        return substr(str_shuffle($chars), 0, $length);
    }
}
