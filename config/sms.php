<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default SMS Provider
    |--------------------------------------------------------------------------
    |
    | Choose the SMS provider you want to use by default.
    | Supported: "dummy", "msg91", "twilio", etc.
    |
    */

    'default' => env('SMS_PROVIDER', 'dummy'),

    /*
    |--------------------------------------------------------------------------
    | Provider Configurations
    |--------------------------------------------------------------------------
    |
    | Store credentials or settings for each SMS provider.
    | These can be pulled from your .env file for security.
    |
    */

    'providers' => [

        'dummy' => [
            // No credentials needed
        ],

        'msg91' => [
            'api_key' => env('MSG91_API_KEY'),
            'sender_id' => env('MSG91_SENDER_ID', 'LARAVL'),
            'route' => env('MSG91_ROUTE', '4'),
        ],

        'twilio' => [
            'sid' => env('TWILIO_SID'),
            'token' => env('TWILIO_TOKEN'),
            'from' => env('TWILIO_FROM'),
        ],
    ],
];
