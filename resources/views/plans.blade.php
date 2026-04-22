@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Messages --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @php
        $user = auth()->user();
        $subscription = $user->subscription('default');
    @endphp

    {{-- Subscription Status --}}
    @if($subscription && $subscription->valid() && !$subscription->onGracePeriod())

        <div class="alert alert-success">
            ✅ Active Subscription
        </div>

        {{-- Show current plan --}}
        <div class="alert alert-info">
            Current Plan:
            @foreach($plans as $plan)
                @if($user->subscribedToPrice($plan->stripe_plan, 'default'))
                    <strong>{{ $plan->name }}</strong>
                @endif
            @endforeach
        </div>

        {{-- Cancel Button --}}
        <form method="POST" action="{{ route('subscription.cancel') }}">
            @csrf
            <button class="btn btn-danger mb-3">
                Cancel Subscription
            </button>
        </form>

    @elseif($subscription && $subscription->onGracePeriod())

        <div class="alert alert-warning">
            ⚠️ Subscription Cancelled (Active until {{ $subscription->ends_at }})
        </div>

    @else

        <div class="alert alert-danger">
            ❌ No Active Subscription
        </div>

    @endif

    <h2>Available Plans</h2>

    @foreach($plans as $plan)
        <div class="card mb-3">
            <div class="card-body">
                <h5>{{ $plan->name }}</h5>
                <p>Price: ${{ number_format($plan->price, 2) }}</p>

                {{-- FINAL CORRECT LOGIC --}}
                @if($user->subscribedToPrice($plan->stripe_plan, 'default'))
                    <button class="btn btn-success" disabled>
                        Current Plan
                    </button>
                @else
                    <a href="{{ route('plans.show', $plan->slug) }}" class="btn btn-primary">
                        Subscribe
                    </a>
                @endif

            </div>
        </div>
    @endforeach

</div>
@endsection