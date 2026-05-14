<?php

namespace App\Providers;

use App\Services\Sms\LogSmsService;
use App\Services\Sms\LSimSmsService;
use App\Services\Sms\SmsAzService;
use App\Services\Sms\SmsServiceInterface;
use App\Services\Sms\TwilioSmsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SmsServiceInterface::class, function () {
            return match (config('sms.driver')) {
                'twilio' => new TwilioSmsService(
                    config('sms.twilio.sid'),
                    config('sms.twilio.token'),
                    config('sms.twilio.from'),
                ),
                'smsaz' => new SmsAzService(
                    config('sms.smsaz.login'),
                    config('sms.smsaz.secret'),
                    config('sms.smsaz.sender'),
                ),
                'lsim' => new LSimSmsService(),
                default => new LogSmsService(),
            };
        });
    }

    public function boot(): void
    {
        //
    }
}
