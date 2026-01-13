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

    // Show subscription page for a selected plan
    public function show(Plan $plan)
    {
        // Create Stripe SetupIntent for the logged-in user
        $intent = auth()->user()->createSetupIntent();

        return view('subscription', compact('plan', 'intent'));
    }

    // Handle subscription creation
   public function subscription(Request $request)
{
    $request->validate([
        'plan' => 'required|exists:plans,id',
        'payment_method' => 'required|string',
    ]);

    $plan = Plan::findOrFail($request->plan);

    $request->user()
        ->newSubscription('default', $plan->stripe_plan)
        ->create($request->payment_method);

    return redirect()
        ->route('plans.index')
        ->with('success', 'Subscription purchased successfully!');
}

}
