<?php

namespace Pterodactyl\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use Pterodactyl\Http\Controllers\Controller;

class GetUserRoleController extends Controller
{
    public function index(Request $request)
    {
        if(isset($request->user()->role)) { $role = $request->user()->role; } else { $role = $request->user()->role; }

        return [
            'success' => true,
            'data' => [
                'role' => $role,
            ],
        ];
    }
}
