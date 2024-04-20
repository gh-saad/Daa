<!-- user_registered_admin_notification.blade.php -->
@component('mail::message')
# New User Registration

A new user has registered with the following details:
- Name: {{ $user->name }}
- Email: {{ $user->email }}

Login to the admin panel to view further details and documents provided by this user.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
