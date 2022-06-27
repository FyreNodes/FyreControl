<?php

namespace Pterodactyl\Http\Controllers\Admin\Billing;

use Stripe\Exception\ApiErrorException;
use Throwable;
use Stripe\StripeClient;
use Illuminate\View\View;
use Pterodactyl\Models\Plan;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Services\Billing\PlanService;
use Pterodactyl\Http\Requests\Admin\Billing\PlanFormRequest;
use Pterodactyl\Contracts\Repository\BillingRepositoryInterface;

class PlanController extends Controller
{
    public AlertsMessageBag $alert;
    public PlanService $planService;
    public BillingRepositoryInterface $billing;

    /**
     * @param AlertsMessageBag $alert
     * @param BillingRepositoryInterface $billing
     * @param PlanService $planService
     */
    public function __construct(PlanService $planService, BillingRepositoryInterface $billing, AlertsMessageBag $alert)
    {
        $this->alert = $alert;
        $this->billing = $billing;
        $this->planService = $planService;
    }

    /**
     * @return View
     */
    public function index(): View
    {
        return view('admin.billing.plans.index', [
            'plans' => DB::table('plans')->orderBy('id', 'ASC')->get()
        ]);
    }

    /**
     * @return View
     */
    public function new(): View
    {
        return view('admin.billing.plans.new');
    }

    public function edit(int $id): View
    {
        return view('admin.billing.plans.edit', [
            'plan' => Plan::query()->where('id', '=', $id)->first()
        ]);
    }

    /**
     * @param PlanFormRequest $request
     * @return RedirectResponse
     * @throws ApiErrorException
     */
    public function store(PlanFormRequest $request): RedirectResponse
    {
        if ($request->input('price') === '0') {
            $this->planService->create($request->input());
        } else {
            $client = new StripeClient(env('STRIPE_CLIENT_SECRET'));
            $prod = $client->products->create([
                'name' => $request->input('name'),
                'description' => "{$request->input('name')} Plan Subscription",
                'shippable' => false,
                'payment_intent_data' => ['receipt_email' => true],
                'default_price_data' => [
                    'currency' => $this->billing->get('config:currency', 'USD'),
                    'unit_amount' => str_replace('.', '', $request->input('price')),
                    'tax_behavior' => 'unspecified',
                    'recurring' => ['interval' => 'month']
                ]
            ]);
            $data = $request->input();
            $data['stripe_id'] = $prod->id;
            $data['price_id'] = $prod->default_price;
            $this->planService->create($data);
        }
        $this->alert->success('Plan has been successfully created.')->flash();
        return redirect()->route('admin.billing.plans');
    }

    /**
     * @param PlanFormRequest $request
     * @param int $id
     * @return RedirectResponse
     * @throws ApiErrorException
     */
    public function update(PlanFormRequest $request, int $id): RedirectResponse
    {
        if ($request->input('price') > '0') {
            $client = new StripeClient(env('STRIPE_CLIENT_SECRET'));
            $meta = Plan::query()->where('id', '=', $id)->select(['stripe_id'])->first();
            $client->products->update($meta->stripe_id, [
                'name' => $request->input('name'),
                'description' => "{$request->input('name')} Plan Subscription"
            ]);
        };
        $data = $request->input();
        $data['id'] = $id;
        $this->planService->update($data);
        $this->alert->success('Plan has been successfully updated.')->flash();
        return redirect()->route('admin.billing.plans');
    }

    /**
     * @param int $id
     * @return RedirectResponse
     * @throws ApiErrorException
     */
    public function delete(int $id): RedirectResponse
    {
        $meta = Plan::query()->where('id', '=', $id)->select(['stripe_id', 'price_id'])->first();
        Plan::query()->where('id', '=', $id)->delete();
        $this->alert->danger("Plan has been successfully deleted from FyreControl. Please go here to finish deleting the product: https://dashboard.stripe.com/products/{$meta->stripe_id}")->flash();
        return redirect()->route('admin.billing.plans');
    }
}



