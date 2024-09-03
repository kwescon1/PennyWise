<?php

namespace App\Services\Auth;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Interfaces\Auth\AuthServiceInterface;

class AuthService implements AuthServiceInterface
{
    /**
     * Register a new user and generate an authentication token.
     *
     * This method creates a new user record in the database using the provided data,
     * generates an authentication token for the user, and returns the user resource
     * along with the token.
     *
     * @param array $data The validated user registration data.
     * @return array An array containing the registered user resource and the authentication token.
     */
    public function register(array $data): array
    {
        $user = User::create($data);

        $token = $this->generateUserToken($user);

        return [
            'user' => new UserResource($user),
            'token' => $token
        ];
    }
    /**
     * Login an existing user
     *
     * This method logs in an existing user using the provided data, generates an auth
     * token for the user, and returns the user resource
     * along with the token.
     *
     * @param array $data The validated user registration data.
     * @return array An array containing the registered user resource and the authentication token.
     */
    public function login(User $user): array
    {

        $token = $this->generateUserToken($user);

        return [
            'user' => new UserResource($user),
            'token' => $token
        ];
    }

    /**
     * Generate an authentication token for the specified user.
     *
     * This method creates a new token for the given user using Laravel's built-in token
     * creation method. The generated token is returned as a plain text string.
     *
     * @param User $user The user for whom the token is being generated.
     * @return string The plain text authentication token.
     */
    private function generateUserToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }
}
