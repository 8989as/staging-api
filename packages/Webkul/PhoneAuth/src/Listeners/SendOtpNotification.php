<?php

namespace Webkul\PhoneAuth\Listeners;

class SendOtpNotification
{
    /**
     * Handle the event.
     *
     * @param string $phone
     * @param mixed $otpModel
     * @return void
     */
    public function handle($phone, $otpModel)
    {
        // Example: send SMS or log OTP
        // \Log::info("OTP sent to {$phone}: {$otpModel->otp}");
    }
}
