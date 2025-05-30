@component('mail::message')
# Two-Factor Code

Your verification code is: **{{ $code }}**

It will expire in 10 minutes.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
