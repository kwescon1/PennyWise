<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Interfaces\Auth\AuthServiceInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle the user registration process.
     *
     * This method is responsible for registering a new user. It accepts
     * a RegisterUserRequest object, which contains validated and sanitized
     * user input data. The method is expected to perform the registration
     * process, such as creating a new user record in the database, and
     * then return an appropriate response.
     *
     * @param RegisterUserRequest $request  The validated and sanitized registration data.
     * @return \Illuminate\Http\JsonResponse The JSON response with the registration result.
     */
    public function register(RegisterUserRequest $request): \Illuminate\Http\JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->created(__('app.registration_successful_verify'), $result);
    }

    /**
     * Handle the user login process.
     *
     * This method processes the login request by validating the provided credentials
     * through the LoginUserRequest. If validation passes, the user's details are retrieved
     * and passed to the AuthService for token generation and any additional login logic.
     * The response includes a message indicating whether the user's email is verified or not.
     *
     * @param LoginUserRequest $request The validated login request containing the user's credentials.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the login result and a success message.
     */
    public function login(LoginUserRequest $request): \Illuminate\Http\JsonResponse
    {
        // The validation process is handled within the form request.
        // Here we just retrieve the user and proceed with token generation.
        $user = $request->validatedUser();

        $results = $this->authService->login($user);


        return response()->success(
            $user->email_verified_at ? __('app.login_successful') : __('app.login_successful_verify'),
            $results
        );
    }
}
