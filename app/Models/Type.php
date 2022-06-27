<?php

namespace Pterodactyl\Models;

use Carbon\Carbon;

/**
 * @property string $name
 * @property string $default_image
 * @property integer $egg
 * @property Carbon $updated_at
 * @property Carbon $created_at
 */

class Type extends Model
{
    protected $table = 'types';

    public static $validationRules = [
        'name' => 'required|string',
        'default_image' => 'required|string',
        'egg' => 'required|integer'
    ];
}
