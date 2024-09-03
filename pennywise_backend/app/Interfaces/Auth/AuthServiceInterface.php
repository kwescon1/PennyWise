<?php

namespace App\Interfaces\Auth;

use App\Models\User;

interface AuthServiceInterface
{

    public function register(array $data): array;
    public function login(User $user): array;
}
