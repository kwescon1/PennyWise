<x-mail::message>
    # Hello {{ $user->name }},

    We are glad you're using {{ config('app.name') }}. To complete your verification, please use the code below:

    ## Your Verification Code: **{{ $otpCode }}**

    Please enter this code in the app to verify your account. The code is valid for a limited time.

    If you did not request this verification, please ignore this email.

    Thanks,<br>
    {{ config('app.name') }} Team
</x-mail::message>
