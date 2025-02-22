<?php

return [
    'database' => [
        'connectionName' => env('RIOS_CONNECTION_NAME'),
    ],

    'self_service' =>
    [
        "url" => env('RIOS_SERVICE_URL'),
        "client_id" => env('RIOS_CLIENT_ID'),
        "client_secret" => env('RIOS_CLIENT_SECRET'),
        "scope" => env('RIOS_SCOPE'),
    ]
];
