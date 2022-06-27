<?php

namespace Pterodactyl\Http\Requests\Api\Client\Account;

use Illuminate\Support\Facades\DB;
use Pterodactyl\Http\Requests\Api\Client\ClientApiRequest;

class StaffRequest extends ClientApiRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        if (!parent::authorize()) {
            return false;
        }

        $user = DB::table('users')->select(['id', 'staff'])->where('id', '=', $this->user()->id)->get();
        if (count($user) < 1) {
            return false;
        }

        if ($user[0]->staff != 1) {
            return false;
        }

        return true;
    }
}
