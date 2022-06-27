<?php

namespace Pterodactyl\Http\Controllers\Admin\Billing;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Exceptions\Model\DataValidationException;
use Pterodactyl\Exceptions\Repository\RecordNotFoundException;
use Pterodactyl\Http\Requests\Admin\Billing\GeneralFormRequest;
use Pterodactyl\Contracts\Repository\BillingRepositoryInterface;

class GeneralController extends Controller
{
    public BillingRepositoryInterface $billing;
    public AlertsMessageBag $alert;

    public function __construct(BillingRepositoryInterface $billing, AlertsMessageBag $alert)
    {
        $this->billing = $billing;
        $this->alert = $alert;
    }

    public function index(): View
    {
        return view('admin.billing.index', [
            'status' => $this->billing->get('config:status', '0'),
            'currency' => $this->billing->get('config:currency', 'USD')
        ]);
    }

    /**
     * @param GeneralFormRequest $request
     * @return RedirectResponse
     * @throws DataValidationException
     * @throws RecordNotFoundException
     */
    public function update(GeneralFormRequest $request): RedirectResponse
    {
        foreach ($request->normalize() as $key => $value) {
            $this->billing->set('config:'.$key, $value);
        }

        $this->alert->success('Billing config has been updated.')->flash();

        return redirect()->route('admin.billing');
    }
}



