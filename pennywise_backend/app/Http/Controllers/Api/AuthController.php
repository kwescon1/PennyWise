<?php

namespace App\Http\Controllers\Api;

use App\Enums\Auth\OtpType;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\VerifyUserRequest;
use App\Interfaces\Auth\AuthServiceInterface;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\PasswordResetRequest;

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
        $results = $this->authService->register($request->validated(), $request->generateOtp());

        $userResource = new UserResource($results['user']);
        $token = $results['token'];

        return response()->created(__('app.registration_successful_verify'), [
            'user' => $userResource,
            'token' => $token
        ]);
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

        $userResource = new UserResource($results['user']);
        $token = $results['token'];


        return response()->success(
            $user->email_verified_at ? __('app.login_successful') : __('app.login_successful_verify'),
            [
                'user' => $userResource,
                'token' => $token
            ]
        );
    }

    /**
     * Send OTP to verify the user.
     *
     * This method generates a one-time password (OTP) and sends it to the
     * authenticated user for verification purposes. It returns a success response
     * if the OTP was sent successfully.
     *
     * @param VerifyUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function otp(VerifyUserRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->getAuthenticatedUser();
        $code = $request->generateOtp();

        $this->authService->sendOtp($user, $code);

        return response()->success(__('app.otp_sent_success'));
    }

    /**
     * Verify the OTP for the authenticated user.
     *
     * This method validates the OTP entered by the user and verifies
     * the OTP for the authenticated user. If the OTP is valid, it returns
     * a success response with the user data.
     *
     * @param VerifyUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(VerifyUserRequest $request): \Illuminate\Http\JsonResponse
    {
        $otp = $request->validatedOtp();
        $user = $request->getAuthenticatedUser();

        $user = $this->authService->verifyOtp($user, $otp);

        return response()->success(__('app.verification_success'), ['user' => new UserResource($user)]);
    }

    /**
     * Send OTP for resetting the password.
     *
     * This method generates a one-time password (OTP) for resetting the
     * user's password and caches the OTP with the user's encrypted data.
     * It then sends the OTP to the user's email. If this is a password reset request,
     * the response indicates success.
     *
     * @param PasswordResetRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetOtp(PasswordResetRequest $request): \Illuminate\Http\JsonResponse
    {
        // Fetch the user to reset the password
        $user = $request->getResetUser();

        // Generate a new OTP code
        $code = $request->generateOtp();

        // Cache the user data using the OTP code
        Cache::put(hash(AuthService::HASH_METHOD, AuthService::AUTH_CACHE_KEY . $code), encrypt($user), AuthService::AUTH_CACHE_SECONDS);

        // Determine if this is a password reset request or success email
        $isRequest = $request->routeIs(config('routes.reset_otp'));

        // Send the OTP to the user
        $this->authService->sendOtp($user, $code, OtpType::PASSWORD_RESET_CODE, $isRequest);

        return response()->success(__('app.reset_password_sent_success'));
    }

    /**
     * Reset the user's password using OTP.
     *
     * This method retrieves the cached user data using the OTP provided
     * by the user, verifies the OTP, and allows the user to reset their password.
     * It decrypts the cached user data, performs the password reset, and returns
     * a success response with the updated user information.
     *
     * @param PasswordResetRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(PasswordResetRequest $request): \Illuminate\Http\JsonResponse
    {
        $validatedData = $request->validated();
        $code = $validatedData['otp'];

        // Generate the cache key using the hashed OTP code
        $cacheKey = hash(AuthService::HASH_METHOD, AuthService::AUTH_CACHE_KEY . $code);

        // Retrieve the cached user
        $user = Cache::get($cacheKey);

        if (!$user) {
            throw ValidationException::withMessages([
                'otp' => __('app.invalid_otp')
            ]);
        }

        // Decrypt the user data
        $decryptedUser = decrypt($user);

        // Reset the password
        $this->authService->resetPassword($decryptedUser, $validatedData);

        return response()->success(__('app.password_reset_success'), ['user' => new UserResource($decryptedUser)]);
    }
}
