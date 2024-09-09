<?php

namespace App\Jobs\Auth;

use App\Models\User;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPasswordResetEmail implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Dispatchable;

    public $user;
    public $otpCode;
    public $isRequest;
    public $tries = 5; // Retry up to 5 times
    public $backoff = 60; // Delay of 60 seconds between retries (backoff)

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $otpCode, bool $isRequest)
    {
        $this->user = $user;
        $this->otpCode = $otpCode;
        $this->isRequest = $isRequest;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send(new PasswordResetMail($this->user, $this->otpCode, $this->isRequest));
    }
}
