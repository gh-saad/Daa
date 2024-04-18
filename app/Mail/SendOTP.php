<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class SendOTP extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $otp; // Added OTP variable

    /**
     * Create a new message instance.
     *
     * @param User $user The newly registered user
     * @param int $otp The OTP code
     * @return void
     */
    public function __construct($user, $otp)
    {
        $this->user = $user;
        $this->otp = $otp; // Assign OTP to the property
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.user.email_verification')
                    ->subject('Email Verification');
    }
}
