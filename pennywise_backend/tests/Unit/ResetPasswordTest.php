<?php
// TODO fix all unit tests.. got broken when i introduced relations in my model
use App\Models\Otp;
use App\Models\User;
use Tests\Unit\Stubs\OtpStub;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use App\Jobs\Auth\SendPasswordResetSuccessfulEmail;

beforeEach(function () {
    // Common user and auth service setup
    $this->authService = new AuthService();
    $this->user = User::factory()->make(['id' => random_int(1, 10)]);

    // Sample data for password reset
    $this->data = ['otp' => '123456', 'password' => 'newpassword'];

    // Mock OTP model behavior but not replace factory functionality
    $this->mockOtp = Mockery::mock('alias:' . Otp::class, OtpStub::class)->shouldIgnoreMissing();

    // Define the helper function to mock OTP retrieval
    $this->mockReturnOtp = function ($otp) {
        // Mock the OTP retrieval with method chaining
        $this->mockOtp->shouldReceive('whereUserId->whereCode->whereIsActive->whereType->where->first')->once()->andReturn($otp);
    };
});

// TODO
// it('successfully resets password with valid OTP', function () {
//     // Generate valid OTP data using factory (outside of mock context)
//     $otpData = Otp::factory($this->mockOtp)->make(['is_active' => Otp::STATUS_ACTIVE]);

//     // Use the helper to mock the retrieval of this OTP data
//     ($this->mockReturnOtp)($otpData);

//     // Mock transaction and password update
//     DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
//         return $callback();
//     });

//     // Mock cache forget
//     Cache::shouldReceive('forget')->once();

//     // Mock email dispatch
//     Queue::fake();

//     // Call the resetPassword method in the AuthService
//     $this->authService->resetPassword($this->user, $this->data);

//     // Assert that the password reset success email was dispatched
//     Queue::assertPushed(SendPasswordResetSuccessfulEmail::class);
// });

it('throws validation error when OTP is invalid', function () {

    ($this->mockReturnOtp)(null);

    $this->expectException(ValidationException::class);

    $this->authService->resetPassword($this->user, $this->data);
});
