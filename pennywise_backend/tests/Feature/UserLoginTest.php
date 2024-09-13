<?php

use App\Models\User;
use Illuminate\Support\Str;

beforeEach(function () {
    // Generate random password
    $this->password = Str::password();

    // Prepare user data for testing
    $this->user = User::factory()->make(['password' => $this->password])->makeVisible('password')->toArray();

    // Helper function for login request
    $this->loginUser = function (array $data) {
        return $this->post(route($this->routeNames['login']), $data);
    };

    // Helper function for login data
    $this->loginData = fn($username, $password) => ['login' => $username, 'password' => $password];

    // Create user in the database for all tests
    User::create($this->user);
});

it('logs in a user, generates token, and returns a login successful message', function () {
    $this->assertDatabaseHas('users', [
        'firstname' => $this->user['firstname'],
        'lastname' => $this->user['lastname']
    ]);

    $response = ($this->loginUser)(($this->loginData)($this->user['username'], $this->password));

    $response->assertOk()
        ->assertJsonPath('message', __('app.login_successful'));

    // Assert the structure of the response
    expect($response->json('data'))->toHaveKeys(['user', 'token']);
});

it('logs user in and prompts user to verify account if not verified', function () {
    // Update email verification status
    User::where('email', $this->user['email'])->update(['email_verified_at' => null]);

    $response = ($this->loginUser)(($this->loginData)($this->user['username'], $this->password));

    $response->assertOk()
        ->assertJsonPath('message', __('app.login_successful_verify'));
});

it('throws an unauthorized error when input is invalid', function () {
    $response = ($this->loginUser)(($this->loginData)($this->user['username'], Str::random(12)));

    $response->assertUnauthorized()->assertJsonPath('error', __('auth.failed'));
});
