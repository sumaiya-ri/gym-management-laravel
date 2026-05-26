<?php

namespace App\Jobs;

use App\Models\Gym;
use App\Models\User;
use App\Mail\SubscriptionConfirmationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendSubscriptionConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $gym;
    protected $user;

    public $tries = 3;
    public $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(Gym $gym, User $user)
    {
        $this->gym = $gym;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->gym || !$this->user) {
            Log::error("SendSubscriptionConfirmationEmail failed: Gym or User model is null.");
            return;
        }

        if (!$this->user->email) {
            Log::error("SendSubscriptionConfirmationEmail failed: Admin user email is missing.");
            return;
        }

        Mail::to($this->user->email)->send(new SubscriptionConfirmationMail($this->gym, $this->user));
        
        Log::info("SaaS Subscription confirmation email sent successfully to Gym Admin {$this->user->email} for Gym ID: {$this->gym->id}");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $gymId = $this->gym ? $this->gym->id : 'Unknown';
        $adminEmail = $this->user ? $this->user->email : 'Unknown';

        Log::error("SaaS Subscription confirmation email job failed. Gym ID: {$gymId}. Admin: {$adminEmail}. Error: {$exception->getMessage()}", [
            'exception' => $exception
        ]);
    }
}
