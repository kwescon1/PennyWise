{{-- prettier-ignore --}}
<x-mail::message>
# Hello {{ $user->name }},

We are glad you're using {{ config('app.name') }}. Your password has been successfully reset.

If you requested this change, you can now log in with your new password.

**Important**: If you did not request this password reset, please contact us immediately as this could indicate
unauthorized access to your account.

For added security, we recommend updating your account information and enabling two-factor authentication if you haven't
already.

Thanks,
{{ config('app.name') }} Team
</x-mail::message>
