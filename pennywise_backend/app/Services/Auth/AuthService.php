<?php

namespace App\Services\Auth;

use App\Models\Otp;
use App\Models\User;
use App\Enums\Auth\OtpType;
use Illuminate\Support\Carbon;
use App\Mail\OtpVerificationMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Interfaces\Auth\AuthServiceInterface;
use Illuminate\Validation\ValidationException;

class AuthService implements AuthServiceInterface
{
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
     * @return void
     */
    public function sendOtp(User $user, string $code): void
    {
        // Store OTP details
        Otp::create([
            'user_id' => $user->id,
            'type' => OtpType::VERIFICATION_CODE,
            'code' => $code,
        ]);

        // Dispatch OTP email
        Mail::to($user)->send(new OtpVerificationMail($user, $code));
    }

    /**
     * Verify the OTP for the given user.
     *
     * This method checks if the provided OTP for the user is valid and active.
     * If valid, it updates the user's email verification timestamp and deactivates the OTP.
     * If the OTP is invalid, a validation exception is thrown.
     *
     * @param  \App\Models\User  $user  The user for whom the OTP is being verified.
     * @param  int  $otp  The OTP code to be verified.
     * @return \App\Models\User  The user object after verification.
     * @throws \Illuminate\Validation\ValidationException If the OTP is invalid.
     */
    public function verifyOtp(User $user, int $otp): User
    {
        // Retrieve the OTP that matches the user ID, code, and is still active
        $otpCode = Otp::whereUserId($user->id)
            ->whereCode($otp)
            ->whereIsActive(Otp::STATUS_ACTIVE)
            ->first();

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
                'email_verified_at' => Carbon::now(),
            ]);

            // Deactivate the OTP by setting is_active to inactive
            $otpCode->update([
                'is_active' => Otp::STATUS_INACTIVE,
            ]);

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
}
