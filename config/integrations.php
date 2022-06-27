<?php

return [
    'discord' => [
        'enabled' => true,
        'client_id' => 'redacted',
        'guild_id' => 'redacted',
        'verified' => 'redacted',
        'client_secret' => 'redacted',
        'callback_url' => env('DISCORD_CALLBACK', 'https://my.fyrenodes.com/auth/integrations/discord/callback'),
        'bot_token' => 'redacted'
    ],
    'github' => [
        'enabled' => true,
        'client_id' => env('GITHUB_CLIENT_ID', 'redacted'),
        'client_secret' => env('GITHUB_CLIENT_SECRET', 'redacted'),
        'callback_url' => env('GITHUB_CALLBACK', 'https://my.fyrenodes.com/auth/integrations/github/callback')
    ]
];
