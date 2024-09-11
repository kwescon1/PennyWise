<?php

namespace Database\Factories;

use App\Models\Otp;
use App\Models\User;
use App\Enums\Auth\OtpType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Otp>
 */
class OtpFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Otp::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),  // Automatically create a related user
            'type' => $this->faker->randomElement([OtpType::VERIFICATION_CODE, OtpType::PASSWORD_RESET_CODE]),
            'code' => $this->faker->numerify('######'), // Generate a 6-digit OTP code
            'is_active' => true, // Default to active OTP
            'expires_at' => Carbon::now()->addMinutes(10), // Set expiration 10 minutes from now
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
