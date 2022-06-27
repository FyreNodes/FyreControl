<?php

namespace Pterodactyl\Models;

/**
 * @property string $code
 * @property string $error
 */

class Error extends Model
{
    protected $table = 'errors';

    public static $validationRules = [
        'code' => 'required|string',
        'error' => 'required|string'
    ];
}
