<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void 
    {
        //
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            function (\Illuminate\Auth\Events\Login $event) {
                try {
                    \App\Services\MongoDBService::collection('login_activity')->insertOne([
                        'user_id' => $event->user->id,
                        'email' => $event->user->email,
                        'role' => $event->user->role,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'created_at' => now()->toDateTimeString(),
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("Could not log login activity to MongoDB: " . $e->getMessage());
                }
            }
        );
    }
}
