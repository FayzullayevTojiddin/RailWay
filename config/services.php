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

    'uzbekvoice' => [
        'api_key' => env('UZBEKVOICE_API_KEY', 'fd384069-9ef1-4f48-8dbe-1e893086572a:32a68e0e-e919-47d1-b056-80dfe2f730e9'),
        'base_url' => env('UZBEKVOICE_BASE_URL', 'https://uzbekvoice.ai'),
        'stt_endpoint' => '/api/v1/stt',
        'tts_endpoint' => '/api/v1/tts',
    ],

];
