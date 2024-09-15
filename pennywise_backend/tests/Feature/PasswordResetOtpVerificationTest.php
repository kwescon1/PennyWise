<?php

use App\Models\Otp;
use App\Models\User;
use App\Enums\Auth\OtpType;
use Illuminate\Support\Str;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use App\Jobs\Auth\SendPasswordResetEmail;
use App\Jobs\Auth\SendPasswordResetSuccessfulEmail;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->otpData = [
        'user_id' => $this->user->id,
        'type' => OtpType::PASSWORD_RESET_CODE,
        'code' => '123456'
    ];

    Queue::fake();
});

it('sends a valid otp to reset password', function () {
    $this->assertDatabaseHas('users', ['username' => $this->user->username]);

    Cache::shouldReceive('put');

    $response = $this->postJson(route($this->routeNames['reset_otp']), ['login' => $this->user['username']]);

    $response->assertOk()->assertJsonPath('message', __('app.reset_password_sent_success'));

    $this->assertDatabaseCount('otps', 1);

    Queue::assertPushed(SendPasswordResetEmail::class);
});

it('throws validation error for incorrect user', function () {
    $response = $this->postJson(route($this->routeNames['reset_otp']), ['login' => fake()->userName()]);

    $response->assertStatus(422)->assertJsonFragment([
        "message" => __('app.validation_failed')
    ]);
});

it('verifies OTP successfully and dispatches email', function () {
    // Encrypt the user and cache it
    Cache::shouldReceive('get')
        ->with(hash(AuthService::HASH_METHOD, AuthService::AUTH_CACHE_KEY . $this->otpData['code']))
        ->andReturn(encrypt($this->user));

    Cache::shouldReceive('forget')
        ->with(hash(AuthService::HASH_METHOD, AuthService::AUTH_CACHE_KEY . $this->otpData['code']));

    Otp::factory()->create($this->otpData);

    $this->assertDatabaseHas('otps', ['code' => $this->otpData['code']]);

    $password = Str::password();

    // Send the password reset request
    $response = $this->postJson(route($this->routeNames['reset_password']), [
        'otp' => $this->otpData['code'],
        'password' => $password,
        'password_confirmation' => $password
    ]);

    $response->assertOk()->assertJsonFragment([
        "message" => __('app.password_reset_success'),
        "firstname" => $this->user->firstname
    ]);

    Queue::assertPushed(SendPasswordResetSuccessfulEmail::class);
});

it('throws validation exception on invalid OTP or password mismatch', function () {
    Otp::factory()->create($this->otpData);

    $this->assertDatabaseHas('otps', ['code' => $this->otpData['code']]);

    $password = Str::password();
    $invalidOtp = random_int(111111, 999999);
    $mismatchedPassword = Str::password();

    // Invalid OTP
    $response = $this->postJson(route($this->routeNames['reset_password']), [
        'otp' => $invalidOtp,
        'password' => $password,
        'password_confirmation' => $password
    ]);

    $response->assertStatus(422)->assertJsonFragment([
        'message' => __('app.validation_failed')
    ]);

    // Password mismatch
    $response = $this->postJson(route($this->routeNames['reset_password']), [
        'otp' => $this->otpData['code'],
        'password' => $password,
        'password_confirmation' => $mismatchedPassword
    ]);

    $response->assertStatus(422)->assertJsonFragment([
        'message' => __('app.validation_failed')
    ]);
});
