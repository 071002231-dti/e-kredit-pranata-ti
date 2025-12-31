<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Main Application Connection
    |--------------------------------------------------------------------------
    |
    | Configuration for connecting to the main e-kredit application
    | to fetch user data, create activities, etc.
    |
    */

    'url' => env('MAIN_APP_URL', 'http://app'),
    'internal_key' => env('MAIN_APP_INTERNAL_KEY'),
    'timeout' => env('MAIN_APP_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Service API Key
    |--------------------------------------------------------------------------
    |
    | API key used by main app to authenticate calls to this service.
    |
    */

    'service_api_key' => env('SERVICE_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Settings for caching user data from main app.
    |
    */

    'cache' => [
        'enabled' => env('MAIN_APP_CACHE_ENABLED', true),
        'ttl' => env('MAIN_APP_CACHE_TTL', 300), // 5 minutes
        'prefix' => 'main_app:',
    ],
];
