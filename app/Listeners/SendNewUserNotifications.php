<?php

namespace App\Listeners;

use App\Events\NewUserRegistered;
use App\Mail\AdminNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class SendNewUserNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(NewUserRegistered $event)
    {
        // Get the selected user ID from the .env file
        $selectedUserId = env('SELECTED_USER_ID');

        // Fetch the user from the database based on the selected user ID
        $user = User::find($selectedUserId);

        // Check if the user exists
        if ($user) {
            // Send email to admin using the email address of the selected user
            Mail::to($user->email)->send(new AdminNotification($event->user));
        } else {
            // Log an error if the user does not exist
            \Log::error("User with ID $selectedUserId does not exist.");
        }
    }
}