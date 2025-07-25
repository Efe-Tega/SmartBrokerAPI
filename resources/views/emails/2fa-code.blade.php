@component('mail::message')
    # Two-Factor Verification Code

    Your verification code is: **{{ $code }}**

    This code will expire in 10 minutes.

    If you did not request this, please contact our support team immediately.

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
