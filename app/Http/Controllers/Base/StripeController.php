<?php

namespace Pterodactyl\Http\Controllers\Base;

use Stripe\Stripe;
use Stripe\Webhook;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Pterodactyl\Exceptions\DisplayException;
use Pterodactyl\Http\Controllers\Controller;
use Stripe\Exception\UnexpectedValueException;
use Stripe\Exception\SignatureVerificationException;
use Pterodactyl\Services\Billing\SubscriptionService;

class StripeController extends Controller
{
    public SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        Stripe::setApiKey(env('STRIPE_CLIENT_SECRET'));
        $payload = $request->getContent();
        try {
            $event = Webhook::constructEvent($payload, $request->headers->get('stripe-signature'), 'whsec_bTulilbcJ126PjrOszUxzO2sqM5YK3te');
        } catch (UnexpectedValueException|SignatureVerificationException $e) {
            return new Response('Error Unvalidated', 401);
        }
        $data = $event->data->toArray()['object'];
        try {
            $msg = match ($event->type) {
                'checkout.session.completed' => $this->completed($data),
                'invoice.paid' => 'Success',
                default => 'Ignored - Unhandled Event',
            };
        } catch (\Exception $e) {
            $msg = "Failed - {$e}";
        }
        return new Response($msg, 200);
    }

    /**
     * @param array $data
     * @return string
     * @throws DisplayException
     */
    private function completed(array $data): string
    {
        if ($data['payment_status'] !== 'paid') return 'Failed - Payment Issue';
        DB::table('temp_subs')->where('sub_id', '=', $data['id'])->update(['sub_id' => $data['subscription']]);
        $subscription = DB::table('temp_subs')->select(['plan', 'user'])->where('sub_id', '=', $data['subscription'])->first();
        $sub_data = [
            'id' => $data['subscription'],
            'plan' => $subscription->plan,
            'user' => $subscription->user
        ];
        $this->subscriptionService->create($sub_data);
        return 'Success - Instance Deployed';
    }
}
