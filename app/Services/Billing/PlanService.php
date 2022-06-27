<?php

namespace Pterodactyl\Services\Billing;

use Carbon\Carbon;
use Pterodactyl\Models\Plan;

class PlanService
{
    public Plan $plan;

    public function __construct(Plan $plan)
    {
        $this->plan = $plan;
    }

    /**
     * @param array $data
     * @return void
     */
    public function create(array $data): void
    {
        $plan = $this->plan;
        if (array_key_exists('stripe_id', $data)) {
            $plan->stripe_id = $data['stripe_id'];
        } else $plan->stripe_id = null;
        if (array_key_exists('price_id', $data)) {
            $plan->price_id = $data['price_id'];
        } else $plan->price_id = null;
        $plan->name = $data['name'];
        $plan->description = $data['description'];
        $plan->image = $data['image'];
        $plan->price = $data['price'];
        $plan->cpu = $data['cpu'];
        $plan->memory = $data['memory'];
        $plan->disk = $data['disk'];
        $plan->swap = $data['swap'];
        $plan->io = $data['io'];
        $plan->databases = $data['databases'];
        $plan->backups = $data['backups'];
        $plan->allocations = $data['allocations'];
        $plan->updated_at = Carbon::now();
        $plan->created_at = Carbon::now();
        Plan::query()->insert($plan->getAttributes());
    }

    /**
     * @param array $data
     * @return void
     */
    public function update(array $data): void
    {
        $plan = $this->plan;
        $plan->name = $data['name'];
        $plan->description = $data['description'];
        $plan->image = $data['image'];
        $plan->price = $data['price'];
        $plan->cpu = $data['cpu'];
        $plan->memory = $data['memory'];
        $plan->disk = $data['disk'];
        $plan->swap = $data['swap'];
        $plan->io = $data['io'];
        $plan->databases = $data['databases'];
        $plan->backups = $data['backups'];
        $plan->allocations = $data['allocations'];
        $plan->updated_at = Carbon::now();
        Plan::query()->where('id', '=', $data['id'])->update($plan->getAttributes());
    }
}
