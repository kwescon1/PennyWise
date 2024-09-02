<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends BaseUserRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            "firstname" => "required|max:255",
            "lastname" => "required|max:255",
            "email" => "required|max:255|unique:users,email|regex:/^[A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,64}$/",
            "username" => "required|max:255|unique:users,username|regex:/^[0-9a-z._]+$/",
            "password" => [
                "required",
                "confirmed",
                Password::min(8)
                    ->max(255)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ];
    }

    // Generate UUID for the user
    public function generateUuid(): string
    {
        return Str::uuid()->toString();
    }

    /**
     * Prepare the data for validation.
     *
     * This method is called before the validation rules are applied. 
     * It sanitizes the username and normalizes the email by merging 
     * the modified values back into the request data. This ensures 
     * that the data is in the correct format before validation occurs.
     *
     * @return void
     */
    public function prepareForValidation()
    {
        $this->merge([
            'username' => $this->sanitizeUsername(),
            'email' => $this->normalizeEmail(),
            'uuid' => $this->generateUuid(), // Automatically generate and add UUID to the request data
        ]);
    }

    public function validated($key = null, $default = null)
    {
        $validatedData  = parent::validated();

        //Add UUID to the validated data
        $validatedData['uuid'] = $this->input('uuid');

        return $validatedData;
    }
}
