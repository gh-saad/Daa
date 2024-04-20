@component('mail::message')
# Hello,

Your account has been approved by the admin, and is now active.
login to your account and start using the system.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
