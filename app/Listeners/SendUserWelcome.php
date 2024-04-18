<?php

namespace App\Listeners;

use App\Events\NewUserWelcome;
use App\Mail\UserWelcome;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class SendUserWelcome implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(NewUserWelcome $event)
    {
        // Fetch the user from the event
        $user = User::find($event->user->id);

        // Check if the user exists
        if ($user) {
            // Send email to user using the email address
            Mail::to($user->email)->send(new UserWelcome($user));
        } else {
            // Log an error if the user does not exist
            \Log::error("User does not exist.");
        }
    }
}