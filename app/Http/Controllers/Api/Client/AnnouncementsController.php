<?php

namespace Pterodactyl\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Pterodactyl\Exceptions\DisplayException;

class AnnouncementsController extends ClientApiController
{
    /**
     * @param Request $request
     * @return array
     */
    public function index(Request $request): array
    {   
        $announcements = DB::table('announcements')->orderBy('updated_at', 'DESC')->get();

        return [
            'success' => true,
            'data' => [
                'announcements' => $announcements,
            ],
        ];
    }

    public function view(Request $request, $id): array
    {   
        $announcements = DB::table('announcements')->orderBy('updated_at', 'DESC')->where('id', '=', $id)->get();

        if (count($announcements) < 1) {
            throw new DisplayException('Announcement not found.');
        }

        return [
            'success' => true,
            'data' => [
                'announcements' => $announcements,
            ],
        ];
    }
}
