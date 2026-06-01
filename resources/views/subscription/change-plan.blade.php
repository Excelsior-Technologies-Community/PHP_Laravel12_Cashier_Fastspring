{{-- resources/views/subscription/change-plan.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Change Subscription Plan</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        Your current plan: <strong>{{ $currentPlanDetails->name ?? 'N/A' }}</strong>
                    </div>
                    
                    <form method="POST" action="{{ route('subscription.change-plan') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Select New Plan</label>
                            <div class="row">
                                @foreach($plans as $plan)
                                    @if($currentPlanDetails && $currentPlanDetails->id != $plan->id)
                                    <div class="col-md-6 mb-3">
                                        <div class="card {{ old('plan_id') == $plan->id ? 'border-primary' : '' }}">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" 
                                                           name="plan_id" value="{{ $plan->id }}" 
                                                           id="plan_{{ $plan->id }}"
                                                           {{ old('plan_id') == $plan->id ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="plan_{{ $plan->id }}">
                                                        <h5>{{ $plan->name }}</h5>
                                                        <p class="mb-0">${{ number_format($plan->price, 2) }}/month</p>
                                                        <small class="text-muted">{{ $plan->description }}</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                            @error('plan_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-warning">
                            <strong>Note:</strong> Changing your plan will prorate the charges. Your new plan will take effect immediately.
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Change Plan</button>
                        <a href="{{ route('subscription.current') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection