<?php

namespace App\Listeners;

use App\Events\EmailVerification;
use App\Mail\SendOTP;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class SendEmailVerification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(EmailVerification $event)
    {
        // generate a OTP to email the user
        $otp = rand(100000, 999999);

        // Save the OTP in the database
        $event->user->email_verification_code = $otp;
        $event->user->save();

        // try to send the email
        try {
                // Send email to the user with the OTP
                Mail::to($event->user->email)->send(new SendOTP($event->user, $otp));
            } catch (\Exception $e) {
                // Log an error
                \Log::error("An error occurred: " . $e->getMessage());
            }
    }
}