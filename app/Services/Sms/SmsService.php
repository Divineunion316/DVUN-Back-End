<?php

namespace App\Services\Sms;

use App\Contracts\SmsServiceInterface;
use Illuminate\Support\Facades\Log;

class SmsService implements SmsServiceInterface
{
    public function send(string $mobile, string $message): bool
    {
        // Just log the message for now (until real provider is added)
        Log::info("SMS to {$mobile}: {$message}");

        // Simulate successful send
        return true;
    }
}
