@component('mail::message')
# Welcome to {{ config('app.name') }}

Thank you for registering with us!

Your OTP code is: {{ $otp }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
