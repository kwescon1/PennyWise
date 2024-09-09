{{-- prettier-ignore --}}
<x-mail::message>
# Hello {{ $user->name }},

@if ($isRequest)
We received a request to reset your password for your {{ config('app.name') }} account.

Please use the code below to proceed:

Your Password Reset Code: **{{ $otpCode }}**

Please enter this code in the app to reset your password. The code is valid for **10 minutes**.

For your security, do not share this code. {{ config('app.name') }} representatives will never ask for this code over the phone or via SMS.

If you did not request a password reset, please ignore this email.
@else
Your password has been successfully reset.

If you did not request this change, please contact our support team
immediately.
@endif

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>
