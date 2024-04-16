<?php

namespace App\Listeners;

use App\Events\NewUserRegistered;
use App\Mail\AdminNotification;
use App\Mail\UserWelcome;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendNewUserNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(NewUserRegistered $event)
    {
        // Send email to admin
        Mail::to('alt.v3-3wui1wa@yopmail.com')->send(new AdminNotification($event->user));

        // Send welcome email to the registered user
        Mail::to($event->user->email)->send(new UserWelcome($event->user));
    }
}