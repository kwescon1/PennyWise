<?php

namespace App\Enums\Auth;

use Illuminate\Contracts\Support\DeferringDisplayableValue;

enum OtpType: string implements DeferringDisplayableValue
{
    case VERIFICATION_CODE = 'verification';
    case PASSWORD_RESET_CODE = 'password_reset';

    /**
     * Check if the current enum case is the verification code.
     *
     * This method compares the current enum instance to the
     * VERIFICATION_CODE case to determine if they are the same.
     *
     * @return bool  True if the enum is VERIFICATION_CODE, otherwise false.
     */
    public function isVerificationCode(): bool
    {
        return $this == self::VERIFICATION_CODE;
    }

    /**
     * Check if the current enum case is the password reset code.
     *
     * This method compares the current enum instance to the
     * PASSWORD_RESET_CODE case to determine if they are the same.
     *
     * @return bool  True if the enum is PASSWORD_RESET_CODE, otherwise false.
     */
    public function isPasswordResetCode(): bool
    {
        return $this == self::PASSWORD_RESET_CODE;
    }

    /**
     * Resolve the displayable value for the enum case.
     *
     * This method returns a human-readable string based on the current
     * enum case. It's useful when you want to display the enum case
     * in a user-friendly format.
     *
     * @return string  The displayable value of the current enum case.
     */
    public function resolveDisplayableValue(): string
    {
        return match (true) {
            $this->isVerificationCode()   => 'Verification Code',
            $this->isPasswordResetCode()  => 'Password Reset Code',
        };
    }
}
