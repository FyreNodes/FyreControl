<?php

namespace Pterodactyl\Http\Controllers\Admin\Billing;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Pterodactyl\Http\Requests\Admin\Billing\TypeFormRequest;
use Pterodactyl\Models\Type;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Services\Billing\TypeService;
use Pterodactyl\Contracts\Repository\BillingRepositoryInterface;

class TypeController extends Controller
{
    public AlertsMessageBag $alert;
    public TypeService $typeService;
    public BillingRepositoryInterface $billing;

    /**
     * @param AlertsMessageBag $alert
     * @param TypeService $typeService
     * @param BillingRepositoryInterface $billing
     */
    public function __construct(AlertsMessageBag $alert, TypeService $typeService, BillingRepositoryInterface $billing)
    {
        $this->alert = $alert;
        $this->billing = $billing;
        $this->typeService = $typeService;
    }

    /**
     * @return View
     */
    public function index(): View
    {
        return view('admin.billing.types.index', [
            'types' => Type::query()->orderBy('id', 'ASC')->get()
        ]);
    }

    /**
     * @return View
     */
    public function new(): View
    {
        return view('admin.billing.types.new');
    }

    /**
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        return view('admin.billing.types.edit', [
            'type' => Type::query()->where('id', '=', $id)->first()
        ]);
    }

    /**
     * @param TypeFormRequest $request
     * @return RedirectResponse
     */
    public function store(TypeFormRequest $request): RedirectResponse
    {
        $this->typeService->create($request->input());
        $this->alert->success('Type has been successfully created.')->flash();
        return redirect()->route('admin.billing.types');
    }

    /**
     * @param TypeFormRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(TypeFormRequest $request, int $id): RedirectResponse
    {
        $data = $request->input();
        $data['id'] = $id;
        $this->typeService->update($data);
        $this->alert->success('Type has been successfully updated.')->flash();
        return redirect()->route('admin.billing.types');
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function delete(int $id): RedirectResponse
    {
        Type::query()->where('id', '=', $id)->delete();
        $this->alert->success('Type has been successfully deleted.')->flash();
        return redirect()->route('admin.billing.types');
    }
}
