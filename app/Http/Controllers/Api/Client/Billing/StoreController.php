<?php

namespace Pterodactyl\Http\Controllers\Api\Client\Billing;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Pterodactyl\Http\Controllers\Api\Client\ClientApiController;
use Pterodactyl\Models\Plan;
use Pterodactyl\Models\Type;

class StoreController extends ClientApiController
{
    /**
     * @return JsonResponse
     */
    public function plans(): JsonResponse
    {
        $plans = Plan::query()->get();
        return new JsonResponse($plans, 200, [], null, true);
    }

    /**
     * @return JsonResponse
     */
    public function types(): JsonResponse
    {
        $types = Type::query()->get();
        return new JsonResponse($types, 200, [], null, true);
    }
}
