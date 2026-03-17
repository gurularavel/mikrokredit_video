<?php

namespace App\Jobs;

use App\Models\Application;
use App\Services\Sms\SmsServiceInterface;
use App\Services\TemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendApplicationSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(private Application $application) {}

    public function handle(SmsServiceInterface $sms): void
    {
        $link          = url('/record/' . $this->application->token);
        $expiryMinutes = (int) config('sms.expiry_minutes', 60);

        $message = TemplateService::render('sms_video_link', [
            'ad'        => $this->application->name,
            'soyad'     => $this->application->surname,
            'ad_soyad'  => $this->application->name . ' ' . $this->application->surname,
            'link'      => $link,
            'muddet'    => $expiryMinutes,
        ]);

        $sent = $sms->send($this->application->phone, $message);

        if ($sent) {
            $this->application->update(['sms_sent_at' => now()]);
        }
    }
}
