<!-- user_registered_user_notification.blade.php -->
@component('mail::message')
# Welcome to {{ config('app.name') }}

Thank you for registering with us!

Here's the file you requested: [Download File](url_to_download)

Thanks,<br>
{{ config('app.name') }}
@endcomponent
