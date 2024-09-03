<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class LoginUserRequest extends BaseUserRequest
{
    // Store the user object after validation
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "login" => "required|max:255",
            "password" => "required|max:255",
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * This method is called before the validation rules are applied.
     * It sanitizes the login input by merging the modified value back into
     * the request data. This ensures that the data is in the correct format
     * before validation occurs.
     *
     * @return void
     */
    public function prepareForValidation()
    {
        $this->merge([
            'login' => $this->sanitizeLoginInput()
        ]);
    }

    /**
     * Retrieve the sanitized login input.
     *
     * This method returns the validated and sanitized login input (either email or username)
     * that will be used in further processing.
     *
     * @return string
     */
    public function getSanitizedLoginInput(): string
    {
        return $this->validated()['login'];
    }

    /**
     * Retrieve the validated password input.
     *
     * This method returns the validated password from the request data.
     * It ensures that only the validated password field is accessed, which
     * is important for maintaining data integrity and security.
     *
     * @return string
     */
    public function validatedPassword(): string
    {
        return $this->validated()['password'];
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
        $login = $this->getSanitizedLoginInput();

        return filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    }

    /**
     * Find the user by the login field (email or username).
     *
     * This method uses the determined login field to search for the user in the database.
     * If a user is found, it returns the user object; otherwise, it returns null.
     *
     * @return \App\Models\User|null
     */
    public function findUser(): ?User
    {
        $field = $this->loginField();

        return User::where($field, $this->getSanitizedLoginInput())->first();
    }

    /**
     * Check if the provided password matches the stored password.
     *
     * This method verifies the user's password by comparing the provided password
     * with the hashed password stored in the database. It returns true if the
     * passwords match, and false otherwise.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function checkPassword(User $user): bool
    {
        return Hash::check($this->validatedPassword(), $user->password);
    }

    /**
     * Validate the user's credentials.
     *
     * This method validates the user's credentials by first finding the user and then
     * checking the password. If the user is not found or the password is incorrect,
     * it throws an authentication exception. If the credentials are valid, the user object
     * is stored for later use.
     *
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateUserCredentials(): void
    {
        $user = $this->findUser();

        if (!$user || !$this->checkPassword($user)) {
            throw new AuthenticationException(__('auth.failed'));
        }

        // Store the user in the request for later use
        $this->user = $user;
    }

    /**
     * Get the authenticated user.
     *
     * This method retrieves the user object that was stored during the validation process.
     * It allows the controller to access the authenticated user after the credentials
     * have been validated.
     *
     * @return \App\Models\User
     */
    public function validatedUser(): User
    {
        $this->validateUserCredentials();

        return $this->user;
    }
}
