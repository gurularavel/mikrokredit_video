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
        } else {
            $result = (new LSimSmsService())->send($phone, $message);

            if (($result['success'] ?? 0) === 0 && config('sms.pg.api_url')) {
                Log::warning('LSim SMS failed, trying Posta Güvercini fallback', [
                    'phone'       => $phone,
                    'lsim_result' => $result,
                ]);
                $result = (new PostaGuverciniSmsService())->send($phone, $message);
                Log::info('Posta Güvercini fallback result', ['phone' => $phone, 'result' => $result]);
            }
        }

        SmsLog::create([
            'merchant_id' => $this->data['merchant_id'] ?? null,
            'session_id'  => $this->data['session_id']  ?? null,
            'phone'       => $phone,
            'message'     => $message,
            'status'      => $result['status'] ?? 'failed',
            'url'         => $this->data['url'] ?? null,
        ]);
    }
}
