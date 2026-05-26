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
        $revenueAggregate = MongoDBService::collection('gym_revenue_analytics')->aggregate([
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

        return view('super-admin.dashboard', compact(
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
}
