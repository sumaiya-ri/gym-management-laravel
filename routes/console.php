<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('saas:seed-demo', function () {
    $plans = [
        'Starter' => 29.00,
        'Professional' => 59.00,
        'Enterprise' => 99.00,
    ];

    $demoGyms = [
        ['name' => 'Elite Fitness Center', 'email' => 'contact@elitefit.com', 'plan' => 'Enterprise'],
        ['name' => 'Powerhouse Gym', 'email' => 'info@powerhouse.com', 'plan' => 'Professional'],
        ['name' => 'Zen Yoga Studio', 'email' => 'hello@zenyoga.com', 'plan' => 'Starter'],
        ['name' => 'Iron & Steel Gym', 'email' => 'admin@ironsteel.com', 'plan' => 'Professional'],
        ['name' => 'Alpha Athletics', 'email' => 'support@alphaathletics.com', 'plan' => 'Enterprise'],
    ];

    $this->info('Starting SaaS Demo data seeding...');

    foreach ($demoGyms as $data) {
        // Create Gym record in SQL database
        $gym = \App\Models\Gym::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'subscription_plan' => $data['plan'],
                'subscription_status' => 'active',
                'subscription_expires_at' => now()->addMonth(),
                'subscription_transaction_id' => 'SUB-' . strtoupper(\Illuminate\Support\Str::random(10)),
                'payment_method' => 'simulated',
                'amount_paid' => $plans[$data['plan']],
                'payment_at' => now(),
            ]
        );

        // Seed successful transaction to MongoDB collections
        \App\Services\MongoDBService::collection('gym_analytics')->insertOne([
            'gym_id' => $gym->id,
            'gym_name' => $gym->name,
            'plan' => $data['plan'],
            'price' => $plans[$data['plan']],
            'status' => 'active',
            'transaction_id' => $gym->subscription_transaction_id,
            'created_at' => now()->toDateTimeString(),
        ]);

        \App\Services\MongoDBService::collection('payment_logs')->insertOne([
            'gym_id' => $gym->id,
            'gym_name' => $gym->name,
            'amount' => $plans[$data['plan']],
            'transaction_id' => $gym->subscription_transaction_id,
            'type' => 'subscription',
            'created_at' => now()->toDateTimeString(),
        ]);
    }

    // Update global growth metrics in MongoDB
    \App\Services\MongoDBService::collection('gym_analytics')->insertOne([
        'gyms_count' => \App\Models\Gym::count(),
        'members_count' => \App\Models\User::where('role', 'member')->count(),
        'trainers_count' => \App\Models\User::where('role', 'trainer')->count(),
        'bookings_count' => \App\Models\Booking::count(),
        'created_at' => now()->toDateTimeString(),
    ]);

    $this->info('SaaS Demo seeding completed successfully!');
})->purpose('Seed mock SaaS gyms and revenue analytics into MongoDB');

