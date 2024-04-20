<!-- user_registered_user_notification.blade.php -->
@component('mail::message')
# Welcome to {{ config('app.name') }}

Thank you for registering with us!
Your account is currently inactive. Please wait for the admin to approve your account.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
