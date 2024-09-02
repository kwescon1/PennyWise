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

        return response()->created(__('app.registration_successful'), $result);
    }
}
