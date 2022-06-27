<?php

namespace Pterodactyl\Models;

/**
 * @property integer $user
 * @property string $sub_id
 * @property integer $plan
 * @property integer $server
 */

class Subscription extends Model
{
    protected $table = 'subscriptions';

    public static $validationRules = [
        'user' => 'required|integer',
        'sub_id' => 'required|string',
        'plan' => 'required|integer',
        'server' => 'required|integer'
    ];
}
