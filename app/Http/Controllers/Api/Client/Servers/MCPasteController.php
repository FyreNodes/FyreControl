<?php

namespace Pterodactyl\Http\Controllers\Api\Client\Servers;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Pterodactyl\Models\Server;
use Illuminate\Support\Facades\Http;
use Pterodactyl\Repositories\Wings\DaemonServerRepository;
use Pterodactyl\Http\Controllers\Api\Client\ClientApiController;
use Pterodactyl\Http\Requests\Api\Client\Servers\ShareLogRequest;

class MCPasteController extends ClientApiController
{
    private DaemonServerRepository $daemonRepository;

    public function __construct(DaemonServerRepository $daemonRepository)
    {
        parent::__construct();

        $this->daemonRepository = $daemonRepository;
    }

    public function humanFileSize($size, $unit = '')
    {
        if ((!$unit && $size >= 1 << 30) || $unit == 'GB') {
            return number_format($size / (1 << 30), 2) . 'GB';
        }
        if ((!$unit && $size >= 1 << 20) || $unit == 'MB') {
            return number_format($size / (1 << 20), 2) . 'MB';
        }
        if ((!$unit && $size >= 1 << 10) || $unit == 'KB') {
            return number_format($size / (1 << 10), 2) . 'KB';
        }

        return number_format($size) . ' bytes';
    }

    public function index(ShareLogRequest $request): array
    {
        $data = $request->input('data');
        return Http::withBody(urldecode($data), 'application/form-data')->post('https://bin.fyrenodes.net/documents')->json();
    }
}
