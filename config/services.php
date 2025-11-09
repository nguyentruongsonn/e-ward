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

    'vietqr' => [
        'bank_id' => env('VIETQR_BANK_ID', 'MB'),
        'account_no' => env('VIETQR_ACCOUNT_NO', '914040399999'),
    ],

    'casso' => [
        'api_key' => env('CASSO_API_KEY', 'AK_CS.1f57ef80bd2d11f0a73fcb966f33aa53.ZbCZFwFUAE2cm31dPyfnRq9k3FVcCLTPPiYCrS4wNt8xQ9DeKu1v75GM5Q6MMQlnggRcZulM'),
        'api_url' => env('CASSO_API_URL', 'https://oauth.casso.vn/v2/transactions'),
    ],

];
