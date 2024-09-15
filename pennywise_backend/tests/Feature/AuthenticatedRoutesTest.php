<?php

use App\Jobs\Auth\SendOtpVerificationEmail;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->protectedRoute = function () {
        return $this->postJson(route($this->routeNames['otp']));
    };
});

test('route can be accessed when user is authenticated', function () {
    Queue::fake();

    // Authenticate the user
    Sanctum::actingAs($this->user);

    // Call the protected route and get the response
    $response = ($this->protectedRoute)();

    // Assert that the request was successful
    $response->assertOk();

    // Assert that an OTP was created
    $this->assertDatabaseCount('otps', 1);

    // Assert that the OTP email job was dispatched
    Queue::assertPushed(SendOtpVerificationEmail::class);
});

test('unauthorized error is thrown when user is unauthenticated', function () {
    // Call the protected route and get the response
    $response = ($this->protectedRoute)();

    // Assert that the user is unauthorized
    $response->assertUnauthorized();
});
