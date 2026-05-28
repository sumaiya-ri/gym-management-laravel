<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\MemberWelcomeMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendMemberWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    public $tries = 3;
    public $backoff = [10, 30, 60];

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
        if (!$this->user || !$this->user->email) {
            Log::error("SendMemberWelcomeEmail failed: User or user email is missing.");
            return;
        }

        try {
            Mail::to($this->user->email)->send(new MemberWelcomeMail($this->user));
            Log::info("Welcome email sent successfully to member: {$this->user->email}");
        } catch (\Throwable $e) {
            Log::error("Mail sending failed in SendMemberWelcomeEmail for user ID: {$this->user->id}. Error: " . $e->getMessage());
            if (config('queue.default') !== 'sync') {
                throw $e;
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $userId = $this->user ? $this->user->id : 'Unknown';
        $userEmail = $this->user ? $this->user->email : 'Unknown';

        Log::error("Welcome email job failed. User ID: {$userId}. Email: {$userEmail}. Error: {$exception->getMessage()}", [
            'exception' => $exception
        ]);
    }
}
