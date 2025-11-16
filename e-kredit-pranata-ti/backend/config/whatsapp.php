<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Cloud API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for WhatsApp Cloud API integration.
    | You'll need to set up a Meta Business account and WhatsApp Business API
    | to get the required credentials.
    |
    */

    /**
     * WhatsApp API Version
     */
    'api_version' => env('WHATSAPP_API_VERSION', 'v21.0'),

    /**
     * WhatsApp API Base URL
     */
    'api_base_url' => env('WHATSAPP_API_BASE_URL', 'https://graph.facebook.com'),

    /**
     * WhatsApp Business Account ID
     * Get this from Meta Business Manager
     */
    'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),

    /**
     * WhatsApp Phone Number ID
     * This is the ID of the phone number you'll send messages from
     */
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),

    /**
     * WhatsApp API Access Token
     * Generate a permanent access token from Meta Business Manager
     */
    'api_token' => env('WHATSAPP_API_TOKEN'),

    /**
     * Webhook Verify Token
     * Create a random string for webhook verification
     */
    'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),

    /**
     * Webhook Secret (for signature verification)
     * This is used to verify that webhooks are coming from Meta
     */
    'webhook_secret' => env('WHATSAPP_WEBHOOK_SECRET'),

    /**
     * API Request Timeout (in seconds)
     */
    'timeout' => env('WHATSAPP_TIMEOUT', 20),

    /**
     * Enable/Disable Webhook Signature Verification
     */
    'verify_webhook_signature' => env('WHATSAPP_VERIFY_SIGNATURE', true),

    /**
     * Message Templates
     * Pre-approved message templates for notifications
     */
    'templates' => [
        'activity_submitted' => env('WHATSAPP_TEMPLATE_ACTIVITY_SUBMITTED', 'activity_submitted'),
        'activity_approved' => env('WHATSAPP_TEMPLATE_ACTIVITY_APPROVED', 'activity_approved'),
        'activity_rejected' => env('WHATSAPP_TEMPLATE_ACTIVITY_REJECTED', 'activity_rejected'),
        'welcome' => env('WHATSAPP_TEMPLATE_WELCOME', 'welcome_message'),
    ],

    /**
     * Default Language Code
     */
    'language_code' => env('WHATSAPP_LANGUAGE_CODE', 'id'),

    /**
     * Media Storage Settings
     */
    'media' => [
        'disk' => env('WHATSAPP_MEDIA_DISK', 'public'),
        'path' => env('WHATSAPP_MEDIA_PATH', 'whatsapp'),
        'max_size' => env('WHATSAPP_MEDIA_MAX_SIZE', 16384), // 16MB in KB
    ],

    /**
     * Queue Settings
     */
    'queue' => [
        'enabled' => env('WHATSAPP_QUEUE_ENABLED', true),
        'connection' => env('WHATSAPP_QUEUE_CONNECTION', 'database'),
        'name' => env('WHATSAPP_QUEUE_NAME', 'whatsapp'),
    ],

    /**
     * Rate Limiting
     */
    'rate_limit' => [
        'enabled' => env('WHATSAPP_RATE_LIMIT_ENABLED', true),
        'max_requests' => env('WHATSAPP_RATE_LIMIT_MAX', 1000), // per month for free tier
    ],

    /**
     * Logging
     */
    'logging' => [
        'enabled' => env('WHATSAPP_LOGGING_ENABLED', true),
        'channel' => env('WHATSAPP_LOG_CHANNEL', 'stack'),
    ],

];
