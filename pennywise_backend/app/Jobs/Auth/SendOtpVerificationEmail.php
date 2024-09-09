<?php

namespace App\Jobs\Auth;

use App\Models\User;
use App\Mail\OtpVerificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendOtpVerificationEmail implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Dispatchable;

    public $tries = 5; // Retry up to 5 times
    public $backoff = 60; // Delay of 60 seconds between retries (backoff)
    public $user;
    public $otpCode;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $otpCode)
    {
        $this->user = $user;
        $this->otpCode = $otpCode;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        Mail::to($this->user->email)->send(new OtpVerificationMail($this->user, $this->otpCode));
    }
}
