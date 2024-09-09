<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * BaseUserRequest class
 *
 * This class serves as a base request for user-related form requests.
 * It provides common helper functions that can be used to sanitize and
 * normalize user input, such as username and email, across different
 * request classes that extend this base.
 */
class BaseUserRequest extends FormRequest
{
    /**
     * Sanitize the username by trimming any extra spaces
     * and converting it to lowercase.
     *
     * @return string
     */
    public function sanitizeUsername(): string
    {
        // Trim whitespace from the username and convert to lowercase
        return trim(strtolower($this->input('username')));
    }

    /**
     * Normalize the email by trimming any extra spaces
     * and converting it to lowercase.
     *
     * @return string
     */
    public function normalizeEmail(): string
    {
        // Trim whitespace from the email and convert to lowercase
        return strtolower(trim($this->input('email')));
    }


    /**
     * Sanitize the login field by trimming any extra spaces
     * and converting it to lowercase.
     *
     * @return string
     */
    public function sanitizeLoginInput(): string
    {
        // Trim whitespace from the login and convert to lowercase
        return trim(strtolower($this->input('login')));
    }

    /**
     * Sanitize the otp field by trimming any extra spaces
     *
     * @return int
     */
    public function sanitizeOtpInput(): int
    {
        return (int) trim($this->input('otp'));
    }

    /**
     * Generate a secure 6-digit OTP (One-Time Password).
     *
     * This method generates a cryptographically secure random 6-digit OTP
     * between 100000 and 999999, suitable for verification purposes.
     *
     * @return string  The generated OTP.
     */
    public function generateOtp(): string
    {
        return random_int(100000, 999999);
    }

    /**
     * Determine if the login input is an email or a username.
     *
     * This method checks whether the provided login input is a valid email
     * address. If it is, the method returns 'email'; otherwise, it returns
     * 'username'. This helps in querying the user by the correct field.
     *
     * @return string
     */
    public function loginField(): string
    {
        $login = $this->sanitizeLoginInput();

        return filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    }
}
