<?php

namespace App\Interfaces\Auth;

use App\Models\User;

interface AuthServiceInterface
{

    public function register(array $data, string $code): array;
    public function login(User $user): array;
    public function sendOtp(User $user, string $code): void;
    public function verifyOtp(User $user, int $otp): User;
}
