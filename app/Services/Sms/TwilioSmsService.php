<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;

class TwilioSmsService implements SmsServiceInterface
{
    public function __construct(
        private string $sid,
        private string $token,
        private string $from,
    ) {}

    public function send(string $phone, string $message): bool
    {
        $response = Http::withBasicAuth($this->sid, $this->token)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json", [
                'To'   => $phone,
                'From' => $this->from,
                'Body' => $message,
            ]);

        return $response->successful();
    }
}
