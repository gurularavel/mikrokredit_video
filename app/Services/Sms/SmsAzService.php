<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;

class SmsAzService implements SmsServiceInterface
{
    public function __construct(
        private string $login,
        private string $secret,
        private string $sender,
    ) {}

    public function send(string $phone, string $message): bool
    {
        $response = Http::post('https://sms.az/api/send', [
            'login'   => $this->login,
            'secret'  => $this->secret,
            'to'      => $phone,
            'text'    => $message,
            'sender'  => $this->sender,
        ]);

        return $response->successful();
    }
}
