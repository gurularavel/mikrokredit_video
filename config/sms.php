<?php

return [
    'driver'         => env('SMS_DRIVER', 'log'),
    'expiry_minutes' => env('SMS_LINK_EXPIRY_MINUTES', 60),

    'twilio' => [
        'sid'   => env('TWILIO_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),
        'from'  => env('TWILIO_FROM'),
    ],

    'smsaz' => [
        'login'  => env('SMSAZ_LOGIN'),
        'secret' => env('SMSAZ_SECRET'),
        'sender' => env('SMSAZ_SENDER', 'INFO'),
    ],

    // L.Sim + Posta Güvercini provider sistemi (SendSmsJob tərəfindən istifadə olunur)
    'provider' => env('SMS_PROVIDER', 'lsim'),

    'lsim' => [
        'api_url'  => env('LSIM_API_URL', 'https://apps.lsim.az/quicksms/v1/send'),
        'api_key'  => env('LSIM_API_KEY', ''),
        'username' => env('LSIM_API_USERNAME', ''),
        'sender'   => env('LSIM_SENDER', 'INFO'),
    ],

    'pg' => [
        'api_url'     => env('PG_API_URL', ''),
        'public_key'  => env('PG_PUBLIC_KEY', ''),
        'private_key' => env('PG_PRIVATE_KEY', ''),
        'sender'      => env('PG_SENDER', 'INFO'),
    ],
];
