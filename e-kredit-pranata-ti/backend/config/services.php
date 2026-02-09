<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Microservice
    |--------------------------------------------------------------------------
    |
    | Configuration for the WhatsApp microservice that handles all
    | WhatsApp Cloud API communication and message handling.
    |
    */
    'whatsapp_service' => [
        'url' => env('WHATSAPP_SERVICE_URL', 'http://whatsapp-service:8080'),
        'key' => env('WHATSAPP_SERVICE_KEY'),
        'timeout' => env('WHATSAPP_SERVICE_TIMEOUT', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Internal API
    |--------------------------------------------------------------------------
    |
    | Configuration for internal service-to-service communication.
    | Used by microservices to call main app APIs.
    |
    */
    'internal_api' => [
        'key' => env('INTERNAL_API_KEY'),
    ],

];
