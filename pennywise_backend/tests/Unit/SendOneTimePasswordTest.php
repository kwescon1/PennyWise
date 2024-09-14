<?php
// TODO fix all unit tests.. got broken when i introduced relations in my model
use App\Models\Otp;
use App\Models\User;
use App\Enums\Auth\OtpType;
use Tests\Unit\Stubs\OtpStub;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Queue;
use App\Jobs\Auth\SendPasswordResetEmail;
use App\Jobs\Auth\SendOtpVerificationEmail;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

// Shared setup logic
beforeEach(function () {
    // Assignment of the mockOtp property using Mockery
    $this->mockOtp = Mockery::mock('alias:' . Otp::class, OtpStub::class);

    // Common user and auth service setup
    $this->authService = new AuthService();
    $this->user = User::factory()->make();

    // Define the helper function as a closure to access $this
    $this->mockOtpCreationAndEmailDispatch = function ($count) {
        // Mock OTP count in the last 30 mins.
        $this->mockOtp->shouldReceive('where->where->count')->andReturn($count);
    };
});

// Test for OTP limit exceeded
it('throws too many request exception when OTP limit is exceeded', function () {
    // Set the OTP count to 3, indicating the limit has been exceeded
    ($this->mockOtpCreationAndEmailDispatch)(3);

    // Expect an exception
    $this->expectException(TooManyRequestsHttpException::class);

    // Call the method
    $this->authService->sendOtp($this->user, "123456");
});

// Test for OTP verification email
it('dispatches OTP verification email when limit is not exceeded', function () {
    // Mock email dispatch
    Queue::fake();

    // Set OTP count to 1, within the allowed limit
    ($this->mockOtpCreationAndEmailDispatch)(1);

    // Mock the creation of the OTP
    $this->mockOtp->shouldReceive('create')->once();

    // Call the method for OTP verification
    $this->authService->sendOtp($this->user, '123456');

    // Assert that the OTP verification email was dispatched
    Queue::assertPushed(SendOtpVerificationEmail::class);
});

// Test for OTP password reset email
it('dispatches OTP password reset email when limit is not exceeded', function () {
    // Mock email dispatch
    Queue::fake();

    // Set OTP count to 1, within the allowed limit
    ($this->mockOtpCreationAndEmailDispatch)(1);

    // Mock the creation of the OTP
    $this->mockOtp->shouldReceive('create')->once();

    // Call the method for password reset
    $this->authService->sendOtp($this->user, '123456', OtpType::PASSWORD_RESET_CODE);

    // Assert that the password reset email was dispatched
    Queue::assertPushed(SendPasswordResetEmail::class);
});
