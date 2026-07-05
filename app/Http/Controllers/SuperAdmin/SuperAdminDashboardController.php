<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Gym;
use App\Models\User;
use App\Models\Booking;
use App\Services\MongoDBService;
use Illuminate\Http\Request;

class SuperAdminDashboardController extends Controller
{
    /**
     * Display the Super Admin Dashboard.
     */
    public function index()
    {
        $totalGyms = Gym::count();
        $totalMembers = User::where('role', 'member')->count();
        $totalTrainers = User::where('role', 'trainer')->count();
        $totalBookings = Booking::count();

        // Query SaaS Revenue from MongoDB Collection 'gym_revenue_analytics' using Aggregation
        $revenueAggregate = MongoDBService::collection('payment_logs')->aggregate([
            [
                '$group' => [
                    '_id' => 'total_revenue',
                    'total' => ['$sum' => '$amount']
                ]
            ]
        ]);
        $totalRevenue = $revenueAggregate[0]['total'] ?? 0.00;

        $activeSubscriptions = Gym::where('subscription_status', 'active')->count();
        $expiredSubscriptions = Gym::where('subscription_status', 'expired')->count();
        $recentGyms = Gym::orderBy('created_at', 'desc')->take(5)->get();

        return view('super-admin.dashboard', compact( //returns blade view super-admin.dashboard
            'totalGyms',
            'totalMembers',
            'totalTrainers',
            'totalBookings',
            'totalRevenue',
            'activeSubscriptions',
            'expiredSubscriptions',
            'recentGyms'
        ));
    }

    /**
     * Get web-authenticated analytics data.
     */
    public function analyticsData()
    {
        $totalGyms = Gym::count();
        $totalMembers = User::where('role', 'member')->count();
        $totalTrainers = User::where('role', 'trainer')->count();
        $totalBookings = Booking::count();

        $revenueAggregate = MongoDBService::collection('payment_logs')->aggregate([
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
        $topGyms = MongoDBService::collection('booking_metrics')->aggregate([
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
        $monthlyRevenue = MongoDBService::collection('payment_logs')->aggregate([
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
        $popularClasses = MongoDBService::collection('booking_metrics')->aggregate([
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
        $subscriptionDistribution = MongoDBService::collection('gym_analytics')->aggregate([
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
