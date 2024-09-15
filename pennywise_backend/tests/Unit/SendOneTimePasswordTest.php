<?php

use App\Models\User;
use App\Enums\Auth\OtpType;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Queue;
use App\Jobs\Auth\SendPasswordResetEmail;
use App\Jobs\Auth\SendOtpVerificationEmail;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

// Shared setup logic for all tests
beforeEach(function () {
    // Initialize AuthService
    $this->authService = new AuthService();

    // Create a mock of the User model
    $this->user = Mockery::mock(User::class);

    // Mock the HasMany relation (otps) between the User and OTP models
    $this->otpRelation = Mockery::mock(Illuminate\Database\Eloquent\Relations\HasMany::class);

    // Mock the Eloquent Builder to simulate query behavior on the OTP model
    $this->otpQueryBuilder = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);

    // Return the mock relation (otps) whenever the otps method is called on the User model
    $this->user->shouldReceive('otps')->andReturn($this->otpRelation);

    // Simulate chaining ActiveOtps and Recent query scopes on the OTP relation
    $this->otpRelation->shouldReceive('activeOtps->recent')
        ->andReturn($this->otpQueryBuilder);

    // Define a helper function to simulate OTP counting logic and mock method calls
    $this->mockOtpCreationAndEmailDispatch = function ($count) {
        // Mock the count method on the OTP query builder to return a specified number of OTPs
        $this->otpQueryBuilder->shouldReceive('count')
            ->andReturn($count);
    };

    // Set up Queue::fake() to intercept and simulate job dispatches for OTP emails
    Queue::fake();
});

// Test for throwing TooManyRequestsHttpException when OTP limit is exceeded
it('throws too many request exception when OTP limit is exceeded', function () {
    // Simulate that 3 OTPs have already been generated in the past 30 minutes
    ($this->mockOtpCreationAndEmailDispatch)(3);

    // Expect the TooManyRequestsHttpException to be thrown due to exceeding the OTP limit
    $this->expectException(TooManyRequestsHttpException::class);

    // Call the sendOtp method on the AuthService, which should throw the exception
    $this->authService->sendOtp($this->user, "123456");
});

// Test to ensure OTP verification email is dispatched when OTP limit is not exceeded
it('dispatches OTP verification email when limit is not exceeded', function () {
    // Ensure Queue::fake() is set up to simulate the job dispatch process
    Queue::fake();

    // Simulate that only 1 OTP has been generated (below the limit)
    ($this->mockOtpCreationAndEmailDispatch)(1);

    // Mock the creation of the OTP in the database
    $this->otpRelation->shouldReceive('create')->once();

    // Call the sendOtp method for OTP verification
    $this->authService->sendOtp($this->user, '123456');

    // Assert that the OTP verification email was dispatched via the SendOtpVerificationEmail job
    Queue::assertPushed(SendOtpVerificationEmail::class);
});

// Test to ensure password reset OTP email is dispatched when OTP limit is not exceeded
it('dispatches OTP password reset email when limit is not exceeded', function () {
    // Ensure Queue::fake() is set up to simulate the job dispatch process
    Queue::fake();

    // Simulate that only 1 OTP has been generated (below the limit)
    ($this->mockOtpCreationAndEmailDispatch)(1);

    // Mock the creation of the OTP in the database
    $this->otpRelation->shouldReceive('create')->once();

    // Call the sendOtp method for password reset
    $this->authService->sendOtp($this->user, '123456', OtpType::PASSWORD_RESET_CODE);

    // Assert that the password reset email was dispatched via the SendPasswordResetEmail job
    Queue::assertPushed(SendPasswordResetEmail::class);
});
