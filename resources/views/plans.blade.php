{{-- resources/views/plans.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <h2>Choose Your Plan</h2>
            <p class="text-muted">Select the perfect plan for your needs</p>
        </div>
    </div>
    
    @if($hasSubscription)
        <div class="alert alert-info text-center mb-4">
            <strong>You currently have an active subscription!</strong> 
            You can manage your subscription from your 
            <a href="{{ route('subscription.current') }}" class="alert-link">subscription dashboard</a>.
        </div>
    @endif

    <div class="row">
        @foreach($plans as $plan)
            <div class="col-md-6 mb-4">
                <div class="card h-100 {{ $currentPlan && $currentPlan->id == $plan->id ? 'border-primary shadow' : '' }}">
                    <div class="card-body text-center">
                        @if($currentPlan && $currentPlan->id == $plan->id)
                            <span class="badge bg-primary mb-3">Current Plan</span>
                        @endif
                        <h3 class="card-title">{{ $plan->name }}</h3>
                        <h2 class="display-4">${{ number_format($plan->price, 2) }}</h2>
                        <p class="text-muted">per month</p>
                        <p class="card-text">{{ $plan->description }}</p>
                        <ul class="list-unstyled mt-3 mb-4">
                            <li>✓ Full Access to Features</li>
                            <li>✓ Priority Support</li>
                            <li>✓ Cancel Anytime</li>
                        </ul>
                        @if(!$hasSubscription || ($currentPlan && $currentPlan->id != $plan->id))
                            <a href="{{ route('plans.show', $plan->slug) }}" class="btn btn-primary btn-lg">
                                {{ $hasSubscription ? 'Switch to ' . $plan->name : 'Subscribe Now' }}
                            </a>
                        @else
                            <button class="btn btn-secondary btn-lg" disabled>Current Plan</button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12 text-center">
            <p class="text-muted">All plans include a 14-day money-back guarantee. No questions asked.</p>
        </div>
    </div>
</div>
@endsection