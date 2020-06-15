<?php

return [
    'api_url' => env('MOMO_API_URL'),
    'oauth' => [
        'url' => env('MOMO_OAUTH_URL', env('MOMO_API_URL').'/oauth/token'),
        'client_id' => env('MOMO_OAUTH_CLIENT_ID'),
        'client_secret' => env('MOMO_OAUTH_CLIENT_SECRET'),
    ],
];