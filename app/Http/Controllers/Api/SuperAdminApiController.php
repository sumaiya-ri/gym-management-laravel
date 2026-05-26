<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use App\Models\User;
use App\Models\Booking;
use App\Services\MongoDBService;
use Illuminate\Http\Request;

class SuperAdminApiController extends Controller
{
    /**
     * Get a list of all gyms.
     */
    public function gyms()
    {
        return response()->json(Gym::all(), 200);
    }

    /**
     * Get SaaS subscriptions status and transaction logs.
     */
    public function subscriptions()
    {
        $activeGyms = Gym::where('subscription_status', 'active')->get();
        $transactions = MongoDBService::collection('subscription_analytics')->find(['status' => 'active']);

        return response()->json([
            'active_gyms' => $activeGyms,
            'transactions' => $transactions,
        ], 200);
    }

    /**
     * Get MongoDB analytics aggregations.
     */
    public function analytics()
    {
        $totalGyms = Gym::count();
        $totalMembers = User::where('role', 'member')->count();
        $totalTrainers = User::where('role', 'trainer')->count();
        $totalBookings = Booking::count();

        $revenueAggregate = MongoDBService::collection('gym_revenue_analytics')->aggregate([
            [
                '$group' => [
                    '_id' => 'total_revenue',
                    'total' => ['$sum' => '$amount']
                ]
            ]
        ]);
        $totalRevenue = $revenueAggregate[0]['total'] ?? 0.00;

        $starterCount = Gym::where('subscription_plan', 'Starter')->count();
        $professionalCount = Gym::where('subscription_plan', 'Professional')->count();
        $enterpriseCount = Gym::where('subscription_plan', 'Enterprise')->count();

        $activeSubs = Gym::where('subscription_status', 'active')->count();
        $expiredSubs = Gym::where('subscription_status', 'expired')->count();

        // 1. Top gyms (by booking count)
        $topGyms = MongoDBService::collection('booking_statistics')->aggregate([
            [
                '$group' => [
                    '_id' => '$gym_name',
                    'bookings_count' => ['$sum' => 1]
                ]
            ],
            [
                '$sort' => ['bookings_count' => -1]
            ],
            [
                '$limit' => 5
            ]
        ]);
        $topGym = $topGyms[0]['_id'] ?? 'N/A';

        // 2. Monthly SaaS revenue
        $monthlyRevenue = MongoDBService::collection('gym_revenue_analytics')->aggregate([
            [
                '$group' => [
                    '_id' => [
                        '$month' => '$created_at'
                    ],
                    'total_revenue' => ['$sum' => '$amount']
                ]
            ],
            [
                '$sort' => ['_id' => 1]
            ]
        ]);

        // 3. Most popular classes
        $popularClasses = MongoDBService::collection('booking_statistics')->aggregate([
            [
                '$group' => [
                    '_id' => '$class_name',
                    'bookings_count' => ['$sum' => 1]
                ]
            ],
            [
                '$sort' => ['bookings_count' => -1]
            ],
            [
                '$limit' => 5
            ]
        ]);

        // 4. Subscription distribution
        $subscriptionDistribution = MongoDBService::collection('subscription_analytics')->aggregate([
            [
                '$group' => [
                    '_id' => '$plan',
                    'count' => ['$sum' => 1]
                ]
            ]
        ]);

        return response()->json([
            'total_gyms' => $totalGyms,
            'total_members' => $totalMembers,
            'total_trainers' => $totalTrainers,
            'total_bookings' => $totalBookings,
            'total_revenue' => $totalRevenue,
            'top_gym' => $topGym,
            'active_subs' => $activeSubs,
            'expired_subs' => $expiredSubs,
            'starter_count' => $starterCount,
            'professional_count' => $professionalCount,
            'enterprise_count' => $enterpriseCount,
            
            'top_gyms' => $topGyms,
            'monthly_revenue' => $monthlyRevenue,
            'popular_classes' => $popularClasses,
            'subscription_distribution' => $subscriptionDistribution,
        ], 200);
    }
}
