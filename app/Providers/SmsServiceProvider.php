<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\SmsServiceInterface;
use App\Services\Sms\SmsService;
// use App\Services\Sms\Msg91SmsService;
// use App\Services\Sms\TwilioSmsService;

class SmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $provider = config('sms.default');

        $implementation = match ($provider) {
            // 'msg91' => Msg91SmsService::class,
            // 'twilio' => TwilioSmsService::class,
            default => SmsService::class,
        };

        $this->app->register(\App\Providers\SmsServiceProvider::class);
    }
}
