<?php

namespace Pterodactyl\Models;

use Carbon\Carbon;

/**
 * @property string $stripe_id
 * @property string $price_id
 * @property string $name
 * @property string $description
 * @property string $image
 * @property string $price
 * @property integer $cpu
 * @property integer $memory
 * @property integer $disk
 * @property integer $swap
 * @property integer $io
 * @property integer $databases
 * @property integer $allocations
 * @property integer $backups
 * @property Carbon $updated_at
 * @property Carbon $created_at
 */

class Plan extends Model
{
    protected $table = 'plans';

    public static $validationRules = [
        'name' => 'required|string',
        'description' => 'required|string',
        'image' => 'required|string',
        'price' => 'required|string|between:1,4',
        'cpu' => 'required|integer|between:0,3',
        'memory' => 'required|integer|between:0,5',
        'disk' => 'required|integer|between:0,8',
        'swap' => 'required|integer|between:0,6',
        'io' => 'required|integer|between:3,3',
        'databases' => 'required|integer|between:0,2',
        'allocations' => 'required|integer|between:0,2',
        'backups' => 'required|integer|between:0,2'
    ];
}
