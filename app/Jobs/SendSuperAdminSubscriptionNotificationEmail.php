<?php

namespace App\Jobs;

use App\Models\Gym;
use App\Mail\SuperAdminSubscriptionNotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendSuperAdminSubscriptionNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $gym;

    public $tries = 1;
    public $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(Gym $gym)
    {
        $this->gym = $gym;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->gym) {
            Log::error("SendSuperAdminSubscriptionNotificationEmail failed: Gym model is null.");
            return;
        }

        $superAdminEmail = 'sumaiyarifkan2@gmail.com';
        sleep(2);
        Mail::to($superAdminEmail)->send(new SuperAdminSubscriptionNotificationMail($this->gym));
        
        Log::info("SaaS Super Admin notification email sent successfully for Gym ID: {$this->gym->id}");
    } 

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $gymId = $this->gym ? $this->gym->id : 'Unknown';

        Log::error("SaaS Super Admin notification email job failed. Gym ID: {$gymId}. Error: {$exception->getMessage()}", [
            'exception' => $exception
        ]);
    }
}
