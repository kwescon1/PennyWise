<?php

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Queue;
use App\Jobs\Auth\SendOtpVerificationEmail;

beforeEach(function () {
    // Helper to create a user array
    $this->userData = function () {
        $user = User::factory()->make()->makeVisible('password')->toArray();
        $user['password'] = Str::password();
        return $user;
    };

    // Helper to perform a registration request
    $this->registerUser = function ($user) {
        return $this->postJson(route($this->routeNames['register']), $user);
    };
});

// Test validation error on invalid input
it('throws validation error when input is invalid', function () {
    $user = ($this->userData)(); // Get user data// password_confirmation field absent

    $response = ($this->registerUser)($user);

    $response->assertUnprocessable(); // Asserts 422 status code
    $response->assertInvalid(['password']); // Asserts password validation error

    $this->assertDatabaseMissing('users', ['email' => $user['email']]); // No user should be created
});

// Test successful registration and OTP email dispatch
it('registers a user and dispatches OTP email', function () {
    Queue::fake(); // Fake the queue to intercept job dispatching

    $user = ($this->userData)(); // Get user data
    $user['password_confirmation'] = $user['password'];

    $response = ($this->registerUser)($user); // Call helper to register the user

    $response->assertCreated();

    // Assert the structure of the response
    expect($response->json('data'))->toHaveKeys(['user', 'token']);

    // Assert user exists in the database
    $this->assertDatabaseHas('users', ['email' => $user['email']]);

    // Assert OTP verification email was dispatched
    Queue::assertPushed(SendOtpVerificationEmail::class);
});
