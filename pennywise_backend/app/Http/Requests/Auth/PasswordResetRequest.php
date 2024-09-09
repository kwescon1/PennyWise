<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Validation\Rules\Password;

class PasswordResetRequest extends BaseUserRequest
{
    /**
     * Store the user object after validation.
     *
     * @var User
     */
    protected $user;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Dynamically validates the 'login' field as either email or username,
     * depending on what the user has provided.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->routeIs(config('routes.reset_otp')) ?
            [
                'login' => ['required', 'string', 'max:255', 'exists:users,' . $this->loginField()],
            ] :  [
                'otp' => 'required|numeric|digits:6',
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

    /**
     * Custom validation error messages.
     *
     * This method returns an array of custom error messages
     * for the validation rules defined in the request.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'login.exists' => "We couldn't find an account with that username or email.",
            'otp.required' => 'An OTP is required for verification.',
            'otp.digits'   => 'The OTP must be exactly 6 digits.',
        ];
    }


    /**
     * Retrieve the user based on the validated input field.
     *
     * This method fetches the user from the database using the
     * validated login field (either 'email' or 'username').
     *
     * @return void
     */
    protected function fetchUser(): void
    {
        $this->user = User::where($this->loginField(), $this->validated()['login'])->first();
    }

    /**
     * Get the validated user for password reset.
     *
     * This method returns the user object that has been fetched
     * from the database for further operations like password reset.
     *
     * @return User The validated user.
     */
    public function getResetUser(): User
    {
        if (!$this->user) {
            $this->fetchUser(); // Ensure user is fetched before returning
        }
        return $this->user;
    }
}
