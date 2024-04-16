<!-- user_registered_admin_notification.blade.php -->
@component('mail::message')
# New User Registration

A new user has registered with the following details:
- Name: {{ $user->name }}
- Email: {{ $user->email }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
