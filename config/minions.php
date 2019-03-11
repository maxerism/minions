<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Project ID
     |--------------------------------------------------------------------------
     |
     | Define the project ID of this application so any request by this app
     | will include the project id information allowing the server to
     | identify the source of a request.
     */

    'id' => null,

    /*
     |--------------------------------------------------------------------------
     | Server Configuration
     |--------------------------------------------------------------------------
     |
     | Define the server configuration including port number, SSL support etc.
     |
     */

    'server' => [
        'port' => env('MINION_SERVER_PORT', 8085),
        'secure' => env('MINION_SERVER_SECURE', false),
        'options' => [
            'tls' => array_filter([
                'local_cert' => env('MINION_SERVER_TLS_CERT', null),
                // 'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_SERVER
            ]),
        ],
    ],


    /*
     |--------------------------------------------------------------------------
     | Projects
     |--------------------------------------------------------------------------
     |
     | List of applications this app will communicate with.
     |
     */

    'projects' => [
        'platform' => [
            'endpoint' => null,
            'token' => null,
            'signature' => null,
            'options' => [],
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | Services
     |--------------------------------------------------------------------------
     |
     | List of services this app can handle as JSON-RPC server.
     |
     */

    'services' => [
        // 'add-user' => App\JsonRpc\AddUserService::class,
    ],
];
