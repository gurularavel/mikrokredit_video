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
];
