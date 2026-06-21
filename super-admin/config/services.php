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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'stripe' => [
        'public'        => env('STRIPE_PUBLIC_KEY'),
        'secret'        => env('STRIPE_SECRET_KEY'),
        'webhook_secret'=> env('STRIPE_WEBHOOK_SECRET'),
        'currency'      => env('STRIPE_CURRENCY', 'usd'),
    ],

    'mtn_momo' => [
        'base_url'         => env('MTN_MOMO_BASE_URL', 'https://sandbox.momodeveloper.mtn.com'),
        'subscription_key' => env('MTN_MOMO_SUBSCRIPTION_KEY'),
        'api_user'         => env('MTN_MOMO_API_USER'),
        'api_key'          => env('MTN_MOMO_API_KEY'),
        'target_env'       => env('MTN_MOMO_TARGET_ENV', 'sandbox'),
        'currency'         => env('MTN_MOMO_CURRENCY', 'EUR'),
        'callback_host'    => env('MTN_MOMO_CALLBACK_HOST'),
    ],

    'airtel_money' => [
        'base_url'        => env('AIRTEL_BASE_URL', 'https://openapiuat.airtel.africa'),
        'client_id'       => env('AIRTEL_CLIENT_ID'),
        'client_secret'   => env('AIRTEL_CLIENT_SECRET'),
        'country'         => env('AIRTEL_COUNTRY', 'UG'),
        'currency'        => env('AIRTEL_CURRENCY', 'UGX'),
    ],

];
