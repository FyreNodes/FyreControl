<?php

namespace Pterodactyl\Http\Requests\Api\Client\Servers;

use Pterodactyl\Http\Requests\Api\Client\ClientApiRequest;

class LogsRequest extends ClientApiRequest
{
    /**
     * @return string
     */
    public function permission()
    {
        return 'logs.view';
    }
}
