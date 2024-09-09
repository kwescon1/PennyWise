<?php

namespace App\Jobs\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetSuccessMail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPasswordResetSucceessfulEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // send password reset success mail
        Mail::to($this->user->email)->send(new PasswordResetSuccessMail($this->user));
    }
}
