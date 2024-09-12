<?php

namespace App\Services\Auth;

use App\Models\Otp;
use App\Models\User;
use App\Enums\Auth\OtpType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Jobs\Auth\SendPasswordResetEmail;
use App\Jobs\Auth\SendOtpVerificationEmail;
use App\Interfaces\Auth\AuthServiceInterface;
use Illuminate\Validation\ValidationException;
use App\Jobs\Auth\SendPasswordResetSucceessfulEmail;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class AuthService implements AuthServiceInterface
{
    public const AUTH_CACHE_KEY = "Auth_";
    public const AUTH_CACHE_SECONDS = 600; // 10 mins
    public const HASH_METHOD = 'sha256';
    public const RETRY_SECONDS = 1800; // 30 mins
    /**
     * Register a new user and generate an authentication token.
     *
     * This method creates a new user record, generates an authentication token,
     * and sends an OTP email for verification.
     *
     * @param array $data The validated user registration data.
     * @param string $code The OTP code generated for verification.
     * @return array An array containing the registered user resource and the authentication token.
     */
    public function register(array $data, string $code): array
    {
        // Creating a new user
        $user = User::create($data);

        // Generate the auth token
        $token = $this->generateUserToken($user);

        // Dispatch OTP for email verification
        $this->sendOtp($user, $code);

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * Login an existing user and generate an authentication token.
     *
     * @param User $user The authenticated user.
     * @return array An array containing the user resource and the authentication token.
     */
    public function login(User $user): array
    {
        // Clear previous tokens
        $user->tokens()->delete();

        $token = $this->generateUserToken($user);

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * Create an OTP and send it to the user via email.
     *
     * @param User $user The user for whom the OTP is being generated.
     * @param string $code The OTP code generated.
     * @param string $type The type of OTP being generated (default: verification).
     * @param bool $isRequest A flag to determine if the OTP request is for resetting a password.
     * @return void
     */
    public function sendOtp(User $user, string $code, OtpType $type = OtpType::VERIFICATION_CODE, bool $isRequest = false): void
    {
        // check for spam and throttle
        $tries = 3;
        $time = Carbon::now()->subMinutes(30);

        // Filtering OTPs created in the last 30 minutes
        $count = $user->otps()->activeOtps($type)->recent($time)->count();

        if ($count >= $tries) {
            throw new TooManyRequestsHttpException(
                self::RETRY_SECONDS, // Retry after 60 seconds
                'You have exceeded the allowed number of attempts. Please try again later.'
            );
        }

        // Store OTP details in the database
        $user->otps()->create([
            'type' => $type,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Dispatch OTP email based on the OTP type
        $this->dispatchEmail($user, $code, $type, $isRequest);
    }

    /**
     * Dispatch the appropriate email based on OTP type.
     *
     * @param User $user The user to whom the OTP email will be sent.
     * @param string $code The OTP code to include in the email.
     * @param string $type The type of OTP (e.g., verification, password reset).
     * @param bool $isRequest A flag to determine if the OTP request is for resetting a password.
     *
     * @return void
     */
    private function dispatchEmail(User $user, string $code, OtpType $type, bool $isRequest): void
    {
        if ($type !== OtpType::PASSWORD_RESET_CODE) {
            // Dispatch the OTP Verification email job
            dispatch(new SendOtpVerificationEmail($user, $code));
        } else {
            // Dispatch the Password Reset email job
            dispatch(new SendPasswordResetEmail($user, $code, $isRequest));
        }
    }

    /**
     * Verify the OTP for the given user.
     *
     * This method checks if the provided OTP for the user is valid, active, and has not expired.
     * By default, it checks OTPs of type OtpType::VERIFICATION_CODE unless specified otherwise.
     * If the OTP is valid, it updates the user's email verification timestamp and deactivates the OTP.
     * If the OTP is invalid or expired, a validation exception is thrown.
     *
     * @param  \App\Models\User  $user  The user for whom the OTP is being verified.
     * @param  int  $otp  The OTP code to be verified.
     * @param  OtpType  $type  The type of OTP being verified. Defaults to OtpType::VERIFICATION_CODE.
     * @return \App\Models\User  The user object after successful verification.
     * @throws \Illuminate\Validation\ValidationException If the OTP is invalid or expired.
     */
    public function verifyOtp(User $user, int $otp, OtpType $type = OtpType::VERIFICATION_CODE): User
    {
        // Retrieve the OTP that matches the user ID, code, and is still active and not expired
        $otpCode = $this->getValidOtp($user, $otp, $type);

        // If no valid OTP is found, throw a validation exception
        if (!$otpCode) {
            throw ValidationException::withMessages([
                'otp' => __('app.invalid_otp')
            ]);
        }

        // Begin transaction to ensure atomicity of the update operations
        return DB::transaction(function () use ($user, $otpCode) {
            // Update the user's email_verified_at field to the current timestamp
            $user->update([
                'email_verified_at' => now(),
            ]);

            // Deactivate the OTP by setting is_active to inactive
            $otpCode->update([
                'is_active' => Otp::STATUS_INACTIVE,
            ]);

            // Return the updated user object
            return $user;
        });
    }


    public function resetPassword(?User $user, array $data): User
    {

        $otpCode = $this->getValidOtp($user, $data['otp'], OtpType::PASSWORD_RESET_CODE);

        // If no valid OTP is found, throw a validation exception
        if (!$otpCode) {
            throw ValidationException::withMessages([
                'otp' => __('app.invalid_otp')
            ]);
        }

        // Begin transaction to ensure atomicity of the update operations
        return DB::transaction(function () use ($user, $otpCode, $data) {
            // Update the user's password field to the current timestamp
            $user->update([
                'password' => $data['password'],
            ]);

            // Deactivate the OTP by setting is_active to inactive
            $otpCode->update([
                'is_active' => Otp::STATUS_INACTIVE,
            ]);

            // forget cache
            Cache::forget(self::HASH_METHOD . self::AUTH_CACHE_KEY . $data['otp']);

            // Dispatch the success email job (queueing it)
            dispatch(new SendPasswordResetSucceessfulEmail($user));

            // Return the updated user object
            return $user;
        });
    }

    /**
     * Generate an authentication token for the user.
     *
     * @param User $user The user for whom the token is generated.
     * @return string The plain-text authentication token.
     */
    private function generateUserToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }

    /**
     * Retrieve a valid OTP for a given user and OTP code.
     *
     * This method fetches a valid OTP for the given user and OTP code.
     * It verifies that the OTP is of the specified type, active, and not expired.
     *
     * @param User $user The user for whom the OTP is being verified.
     * @param int $otp The OTP code to be verified.
     * @param OtpType $type The type of OTP being verified. Defaults to OtpType::VERIFICATION_CODE.
     * @return Otp|null Returns the OTP if it exists, is active, and has not expired; otherwise, returns null.
     */
    private function getValidOtp(User $user, int $otp, OtpType $type = OtpType::VERIFICATION_CODE): ?Otp
    {
        return $user->otps()->whereCode($otp)->activeOtps($type)->notExpired()->first();
    }
}
