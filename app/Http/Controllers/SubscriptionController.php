<?php
// app/Http/Controllers/SubscriptionController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Show current subscription details
     */
    public function current()
    {
        $user = Auth::user();
        $subscription = $user->subscription('default');
        $invoices = $user->invoices();
        
        return view('subscription.current', compact('user', 'subscription', 'invoices'));
    }
    
    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        $user = Auth::user();
        $subscription = $user->subscription('default');
        
        if ($subscription && $subscription->active()) {
            $subscription->cancel();
            
            return redirect()->route('subscription.current')
                ->with('success', 'Your subscription has been cancelled. You will have access until ' . $subscription->ends_at->format('M d, Y'));
        }
        
        return redirect()->route('subscription.current')
            ->with('error', 'No active subscription found.');
    }
    
    /**
     * Resume cancelled subscription
     */
    public function resume(Request $request)
    {
        $user = Auth::user();
        $subscription = $user->subscription('default');
        
        if ($subscription && $subscription->onGracePeriod()) {
            $subscription->resume();
            
            return redirect()->route('subscription.current')
                ->with('success', 'Your subscription has been resumed successfully!');
        }
        
        return redirect()->route('subscription.current')
            ->with('error', 'Unable to resume subscription. It may have expired.');
    }
    
    /**
     * Show change plan form
     */
    public function changePlanForm()
    {
        $user = Auth::user();
        $currentPlan = $user->subscription('default')->stripe_plan;
        $plans = Plan::all();
        
        // Get plan details for current subscription
        $currentPlanDetails = Plan::where('stripe_plan', $currentPlan)->first();
        
        return view('subscription.change-plan', compact('plans', 'currentPlanDetails'));
    }
    
    /**
     * Change subscription plan (swap)
     */
    public function changePlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id'
        ]);
        
        $user = Auth::user();
        $newPlan = Plan::findOrFail($request->plan_id);
        
        $subscription = $user->subscription('default');
        
        if ($subscription && $subscription->active()) {
            $subscription->swap($newPlan->stripe_plan);
            
            return redirect()->route('subscription.current')
                ->with('success', 'Your plan has been changed to ' . $newPlan->name . ' successfully!');
        }
        
        return redirect()->route('subscription.current')
            ->with('error', 'Unable to change plan. Please contact support.');
    }
    
    /**
     * Show payment method update form
     */
    public function paymentMethodForm()
    {
        $user = Auth::user();
        $intent = $user->createSetupIntent();
        
        return view('subscription.payment-method', compact('intent'));
    }
    
    /**
     * Update payment method
     */
    public function updatePaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string'
        ]);
        
        $user = Auth::user();
        
        try {
            $user->updateDefaultPaymentMethod($request->payment_method);
            
            return redirect()->route('subscription.current')
                ->with('success', 'Payment method updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('subscription.current')
                ->with('error', 'Failed to update payment method: ' . $e->getMessage());
        }
    }
}