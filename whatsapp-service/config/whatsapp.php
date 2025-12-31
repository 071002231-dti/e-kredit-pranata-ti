<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Cloud API Configuration
    |--------------------------------------------------------------------------
    */

    'api' => [
        'version' => env('WHATSAPP_API_VERSION', 'v21.0'),
        'base_url' => env('WHATSAPP_API_BASE_URL', 'https://graph.facebook.com'),
        'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'token' => env('WHATSAPP_API_TOKEN'),
        'timeout' => env('WHATSAPP_TIMEOUT', 20),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    */

    'webhook' => [
        'verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
        'secret' => env('WHATSAPP_WEBHOOK_SECRET'),
        'verify_signature' => env('WHATSAPP_VERIFY_SIGNATURE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Settings
    |--------------------------------------------------------------------------
    */

    'queue' => [
        'enabled' => env('WHATSAPP_QUEUE_ENABLED', true),
        'connection' => env('QUEUE_CONNECTION', 'redis'),
        'webhooks' => 'webhooks',
        'notifications' => 'notifications',
        'sync' => 'sync',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */

    'rate_limit' => [
        'enabled' => env('WHATSAPP_RATE_LIMIT_ENABLED', true),
        'max_requests' => env('WHATSAPP_RATE_LIMIT_MAX', 1000),
        'per_minutes' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Message Settings
    |--------------------------------------------------------------------------
    */

    'language_code' => env('WHATSAPP_LANGUAGE_CODE', 'id'),
];
