<?php

namespace App\Interfaces\Auth;

use App\Models\User;
use App\Enums\Auth\OtpType;

interface AuthServiceInterface
{

    public function register(array $data, string $code): array;
    public function login(User $user): array;
    public function sendOtp(User $user, string $code, $type = OtpType::VERIFICATION_CODE): void;
    public function verifyOtp(User $user, int $otp): User;
}
