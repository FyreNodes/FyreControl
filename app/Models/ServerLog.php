<?php

namespace Pterodactyl\Models;

use Carbon\Carbon;

/**
 * @property int $id
 */
class ServerLog extends Model
{
    public const RESOURCE_NAME = 'server_log';
    protected $table = 'server_logs';
    public $timestamps = false;
    protected $guarded = ['id', 'timestamp'];

    public static $validationRules = [
        ''
    ];
}
