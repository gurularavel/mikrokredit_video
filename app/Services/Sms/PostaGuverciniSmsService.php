<?php

namespace App\Services\Sms;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class PostaGuverciniSmsService
{
    protected Client $httpClient;
    protected string $apiUrl;
    protected string $publicKey;
    protected string $privateKey;
    protected string $sender;

    public function __construct()
    {
        $this->httpClient = new Client;
        $this->apiUrl     = config('sms.pg.api_url');
        $this->publicKey  = config('sms.pg.public_key');
        $this->privateKey = config('sms.pg.private_key');
        $this->sender     = config('sms.pg.sender');
    }

    public function send(string $phone, string $message): array
    {
        $phone = '994' . $phone;

        $url = rtrim($this->apiUrl, '/') . '/gateway/api/sms/v1/message/send'
            . '?publicKey=' . urlencode($this->publicKey);

        try {
            $response = $this->httpClient->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->privateKey,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'Text'    => $message,
                    'Purpose' => 'INF',
                    'Options' => [
                        'Originator'  => $this->sender,
                        'SendTime'    => null,
                        'ExpireTime'  => null,
                        'Encoding'    => 'LATIN',
                        'SmsType'     => 'SMS',
                        'ReportLabel' => null,
                    ],
                    'Receivers' => [
                        ['Receiver' => $phone],
                    ],
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (($body['Status'] ?? null) === 200) {
                return ['success' => 1, 'status' => 'sent'];
            }

            return [
                'success' => 0,
                'status'  => 'failed',
                'message' => $body['Description'] ?? 'Unknown error',
            ];
        } catch (RequestException $e) {
            Log::error('PostaGuverciniSmsService error', ['phone' => $phone, 'error' => $e->getMessage()]);

            return ['success' => 0, 'status' => 'failed', 'message' => $e->getMessage()];
        }
    }
}
