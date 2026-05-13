<?php

namespace App\Services\Sms;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class LSimSmsService
{
    protected Client $httpClient;
    protected string $apiUrl;
    protected string $apiKey;
    protected string $username;
    protected string $sender;

    public function __construct()
    {
        $this->httpClient = new Client;
        $this->apiUrl     = config('sms.lsim.api_url');
        $this->apiKey     = config('sms.lsim.api_key');
        $this->username   = config('sms.lsim.username');
        $this->sender     = config('sms.lsim.sender');
    }

    public function send(string $phone, string $message): array
    {
        $phone = '994' . $phone;

        $passwordHashed = md5($this->apiKey);
        $key = md5($passwordHashed . $this->username . $message . $phone . $this->sender);

        $url = $this->apiUrl . '?' . http_build_query([
            'login'   => $this->username,
            'key'     => $key,
            'msisdn'  => $phone,
            'sender'  => $this->sender,
            'text'    => $message,
            'unicode' => 0,
        ]);

        try {
            $response = $this->httpClient->get($url);
            $body     = json_decode($response->getBody()->getContents(), true);

            if (empty($body['errorCode'])) {
                return ['success' => 1, 'status' => 'sent'];
            }

            return ['success' => 0, 'status' => 'failed', 'message' => $body['errorCode']];
        } catch (RequestException $e) {
            $message = $e->hasResponse()
                ? $e->getResponse()->getReasonPhrase()
                : $e->getMessage();

            Log::error('LSimSmsService error', ['phone' => $phone, 'error' => $message]);

            return ['success' => 0, 'status' => 'failed', 'message' => $message];
        }
    }
}
