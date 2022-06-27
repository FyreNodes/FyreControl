<?php

namespace Pterodactyl\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Http\Controllers\Controller;

class StaffController extends Controller
{
    /**
     * @var \Prologue\Alerts\AlertsMessageBag
     */
    protected $alert;

    /**
     * StaffController constructor.
     * @param AlertsMessageBag $alert
     */
    public function __construct(AlertsMessageBag $alert)
    {
        $this->alert = $alert;
    }

    /**
     * @param Request $request
     * @param $userId
     * @return \Illuminate\Http\RedirectResponse
     * @throws 270 \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $userId)
    {
        $userId = (int) $userId;

        $this->validate($request, [
            'staff' => 'required|integer|min:0|max:1'
        ]);

        DB::table('users')->where('id', '=', $userId)->update([
            'staff' => $request->input('staff')
        ]);

        $this->alert->success('You have successfully updated this setting.')->flash();

        return redirect()->route('admin.users.view', $userId);
    }
}
