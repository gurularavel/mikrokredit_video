<?php

namespace App\Jobs;

use App\Models\SmsLog;
use App\Services\Sms\LSimSmsService;
use App\Services\Sms\PostaGuverciniSmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $tries   = 1;
    public int $timeout = 10;

    public function __construct(public array $data) {}

    public function uniqueId(): string
    {
        return ($this->data['session_id'] ?? '') . '_' . ($this->data['phone'] ?? '');
    }

    public function handle(): void
    {
        $provider = config('sms.provider', 'lsim');
        $phone    = $this->data['phone'];
        $message  = $this->data['message'];

        if ($provider === 'postaGuvercini') {
            $result = (new PostaGuverciniSmsService())->send($phone, $message);
            $success = ($result['success'] ?? 0) === 1;
        } else {
            $success = (new LSimSmsService())->send($phone, $message);

            if (!$success && config('sms.pg.api_url')) {
                Log::warning('LSim SMS failed, trying Posta Güvercini fallback', ['phone' => $phone]);
                $result  = (new PostaGuverciniSmsService())->send($phone, $message);
                $success = ($result['success'] ?? 0) === 1;
                Log::info('Posta Güvercini fallback result', ['phone' => $phone, 'success' => $success]);
            }
        }

        SmsLog::create([
            'merchant_id' => $this->data['merchant_id'] ?? null,
            'session_id'  => $this->data['session_id']  ?? null,
            'phone'       => $phone,
            'message'     => $message,
            'status'      => $success ? 'sent' : 'failed',
            'url'         => $this->data['url'] ?? null,
        ]);
    }
}
