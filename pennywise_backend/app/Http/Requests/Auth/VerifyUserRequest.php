<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VerifyUserRequest extends BaseUserRequest
{
    /**
     * The authenticated user object.
     *
     * @var \App\Models\User|null
     */
    protected $authenticatedUser;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Apply OTP validation rules only for the verify route
        return $this->routeIs(config('routes.verify')) ? [
            'otp' => 'required|numeric|digits:6',
        ] : [];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'otp.required' => 'An OTP is required for verification.',
            'otp.digits'   => 'The OTP must be exactly 6 digits.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * This method is called before the validation rules are applied.
     * It sanitizes the code input by merging the modified value back into
     * the request data. This ensures that the data is in the correct format
     * before validation occurs.
     *
     * @return void
     */
    public function prepareForValidation()
    {
        $this->merge([
            'otp' => $this->sanitizeOtpInput()
        ]);
    }

    /**
     * Retrieve the validated otp input.
     *
     * This method returns the validated otp from the request data.
     * It ensures that only the validated otp field is accessed, which
     * is important for maintaining data integrity and security.
     *
     * @return int
     */
    public function validatedOtp(): int
    {
        return (int) $this->validated()['otp'];
    }

    /**
     * Retrieve and store the authenticated user.
     *
     * This method checks if there is a logged-in user, and if not, it throws a NotFoundHttpException.
     * The authenticated user is then stored in the `authenticatedUser` property.
     *
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function fetchAuthenticatedUser(): void
    {
        $user = Auth::user();

        if (!$user) {
            throw new NotFoundHttpException(__('app.auth_user_not_found')); // Throw exception if no authenticated user
        }

        $this->authenticatedUser = $user;
    }

    /**
     * Get the authenticated user.
     *
     * This method returns the stored authenticated user object.
     * It should be called after `fetchAuthenticatedUser()` has been executed.
     *
     * @return \App\Models\User|null
     */
    public function getAuthenticatedUser(): ?\App\Models\User
    {
        $this->fetchAuthenticatedUser();

        return $this->authenticatedUser;
    }
}
