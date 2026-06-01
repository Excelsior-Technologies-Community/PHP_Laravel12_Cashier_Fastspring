<?php
// app/Http/Controllers/PlanController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PlanController extends Controller
{
    /**
     * Show all plans
     */
    public function index()
    {
        $plans = Plan::all();
        $user = auth()->user();
        
        // Check if user has an active subscription
        $hasSubscription = $user && $user->subscribed('default');
        $currentPlan = null;
        
        if ($hasSubscription) {
            $stripePlan = $user->subscription('default')->stripe_plan;
            $currentPlan = Plan::where('stripe_plan', $stripePlan)->first();
        }
        
        return view('plans', compact('plans', 'hasSubscription', 'currentPlan'));
    }
    
    /**
     * Show subscription page for a selected plan
     */
    public function show(Plan $plan)
    {
        $user = auth()->user();
        
        // Check if user already has an active subscription
        if ($user->subscribed('default')) {
            return redirect()->route('subscription.current')
                ->with('info', 'You already have an active subscription. You can change your plan from the subscription management page.');
        }
        
        // Create Stripe SetupIntent for the logged-in user
        $intent = $user->createSetupIntent();
        
        return view('subscription.checkout', compact('plan', 'intent'));
    }
    
    /**
     * Handle subscription creation
     */
    public function subscription(Request $request)
    {
        $request->validate([
            'plan' => 'required|exists:plans,id',
            'payment_method' => 'required|string',
        ]);
        
        $plan = Plan::findOrFail($request->plan);
        $user = $request->user();
        
        try {
            $user->newSubscription('default', $plan->stripe_plan)
                ->create($request->payment_method);
            
            return redirect()
                ->route('subscription.current')
                ->with('success', 'Subscription purchased successfully! Welcome to ' . $plan->name . ' plan.');
        } catch (\Exception $e) {
            return redirect()
                ->route('plans.index')
                ->with('error', 'Failed to create subscription: ' . $e->getMessage());
        }
    }
}