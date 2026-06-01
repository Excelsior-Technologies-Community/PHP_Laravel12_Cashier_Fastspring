<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Subscription;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $activeSubscriptions = User::whereHas('subscriptions', function($query) {
            $query->active();
        })->count();
        
        $totalRevenue = 0;
        $monthlyRevenue = 0;
        
        Stripe::setApiKey(config('cashier.secret'));
        
        try {
            $subscriptions = Subscription::all(['limit' => 100]);
            foreach ($subscriptions as $subscription) {
                if ($subscription->status === 'active') {
                    $totalRevenue += $subscription->items->data[0]->price->unit_amount / 100;
                }
            }
            
            // Get current month revenue
            $startOfMonth = strtotime(date('Y-m-01'));
            $subscriptionsThisMonth = Subscription::all([
                'limit' => 100,
                'created' => ['gte' => $startOfMonth]
            ]);
            
            foreach ($subscriptionsThisMonth as $subscription) {
                if ($subscription->status === 'active') {
                    $monthlyRevenue += $subscription->items->data[0]->price->unit_amount / 100;
                }
            }
        } catch (\Exception $e) {
            // Handle API errors
        }
        
        $plans = Plan::all();
        
        return view('admin.dashboard', compact(
            'totalUsers', 
            'activeSubscriptions', 
            'totalRevenue', 
            'monthlyRevenue',
            'plans'
        ));
    }
    
    public function users()
    {
        $users = User::with('subscriptions')->paginate(20);
        return view('admin.users', compact('users'));
    }
    
    public function subscriptions()
    {
        $users = User::whereHas('subscriptions')->with('subscriptions')->get();
        return view('admin.subscriptions', compact('users'));
    }
    
    public function revenue()
    {
        return view('admin.revenue');
    }
}