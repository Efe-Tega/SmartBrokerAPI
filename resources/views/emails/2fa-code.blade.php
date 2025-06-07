@component('mail::message')
    # Two-Factor Verification Code

    Your verification code is: **{{ $code }}**

    @if ($amount)
        This code is to confirm a withdrawal of **{{ number_format($amount, 2) }}**.
    @endif

    This code will expire in 10 minutes.

    If you did not request this, please contact our support team immediately.

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
