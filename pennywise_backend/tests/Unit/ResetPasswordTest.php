<?php

use App\Models\Otp;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use App\Jobs\Auth\SendPasswordResetSuccessfulEmail;

beforeEach(function () {
    // Mock the HasMany relation (otps) between the User and OTP models
    $this->otpRelation = Mockery::mock(\Illuminate\Database\Eloquent\Relations\HasMany::class);

    // Mock the Eloquent Builder to simulate query behavior on the OTP model
    $this->otpQueryBuilder = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);

    // Initialize AuthService
    $this->authService = new AuthService();

    // Mock the User model
    $this->user = Mockery::mock(User::class);

    // Mock the OTP model
    $this->otpModel = Mockery::mock(Otp::class);

    // Mock the behavior of the otps relationship on the user model
    $this->user->shouldReceive('otps')->andReturn($this->otpRelation);

    // Mock the query chain on the OTP relation
    $this->otpRelation->shouldReceive('whereCode->activeOtps->notExpired')->once()->andReturn($this->otpQueryBuilder);

    // Define a helper function to mock OTP retrieval
    $this->mockReturnOtp = function ($otp) {
        // Mock the first() method to return the OTP object
        $this->otpQueryBuilder->shouldReceive('first')->once()->andReturn($otp);
    };
});

/**
 * Test: Successfully reset password with a valid OTP
 * This test simulates the behavior when a valid OTP is provided,
 * ensuring that the user's password is updated, the OTP is deactivated,
 * and the success email is sent.
 */
it('successfully resets password with valid OTP', function () {
    // Mock OTP retrieval
    ($this->mockReturnOtp)($this->otpModel);

    // Mock transaction behavior
    DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
        return $callback();
    });

    // Mock user and OTP update methods
    $this->user->shouldReceive('update')->once();
    $this->otpModel->shouldReceive('update')->once();

    // Mock cache forget behavior
    Cache::shouldReceive('forget')->once();

    // Mock email dispatch
    Queue::fake();

    // Call the resetPassword method in the AuthService
    $this->authService->resetPassword($this->user, ['otp' => '123456', 'password' => '6786ghjfg@jhn.']);

    // Assert the email job was pushed to the queue
    Queue::assertPushed(SendPasswordResetSuccessfulEmail::class);

    // Assert that the user model called the `otps()` relation and the `update()` method
    $this->user->shouldHaveReceived('otps')->once();
    $this->user->shouldHaveReceived('update')->once();
    $this->otpModel->shouldHaveReceived('update')->once();
});

/**
 * Test: Throws validation error when OTP is invalid
 * This test checks if the system correctly throws a ValidationException
 * when an invalid or non-existent OTP is provided during password reset.
 */
it('throws validation error when OTP is invalid', function () {
    // Simulate OTP retrieval returning null (invalid OTP)
    ($this->mockReturnOtp)(null);

    // Expect a validation exception
    $this->expectException(ValidationException::class);

    // Call the resetPassword method in the AuthService
    $this->authService->resetPassword($this->user, ['otp' => '123456', 'password' => '6786ghjfg@jhn.']);
});
