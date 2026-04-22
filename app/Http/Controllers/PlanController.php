<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;


class PlanController extends Controller
{
    // Show all plans
    public function index()
    {
        $plans = Plan::all();
        return view('plans', compact('plans'));
    }

    // Show subscription page
    public function show(Plan $plan)
    {
        $intent = auth()->user()->createSetupIntent();
        return view('subscription', compact('plan', 'intent'));
    }

    // Create subscription
    public function subscription(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'plan' => 'required|exists:plans,id',
            'payment_method' => 'required|string',
        ]);

        $plan = Plan::findOrFail($request->plan);

        //  Prevent subscribing to same plan again
        if ($user->subscribedToPrice($plan->stripe_plan, 'default')) {
            return back()->with('error', 'You are already subscribed to this plan!');
        }

        // Create subscription (will replace old one if needed)
        $user->newSubscription('default', $plan->stripe_plan)
            ->create($request->payment_method);

        return redirect()->route('plans.index')
            ->with('success', 'Subscription purchased successfully!');
    }

    // Cancel subscription
    public function cancel(Request $request)
    {
        $user = $request->user();

        if ($user->subscribed('default')) {
            $user->subscription('default')->cancel();
        }

        return redirect()->route('plans.index')
            ->with('success', 'Subscription cancelled successfully!');
    }
}