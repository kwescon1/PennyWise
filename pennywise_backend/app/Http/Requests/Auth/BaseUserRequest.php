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
}
