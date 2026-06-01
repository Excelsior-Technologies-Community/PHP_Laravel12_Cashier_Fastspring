{{-- resources/views/subscription/checkout.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Subscribe to {{ $plan->name }} Plan</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Plan Details:</strong><br>
                        Price: ${{ number_format($plan->price, 2) }}/month<br>
                        {{ $plan->description }}
                    </div>
                    
                    <form id="subscription-form" method="POST" action="{{ route('subscription.create') }}">
                        @csrf
                        <input type="hidden" name="plan" value="{{ $plan->id }}">
                        <input type="hidden" name="payment_method" id="payment_method">
                        
                        <div class="mb-3">
                            <label class="form-label">Card Details</label>
                            <div id="card-element" class="form-control"></div>
                            <div id="card-errors" class="text-danger mt-2" role="alert"></div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" target="_blank">Terms of Service</a> and 
                                <a href="#" target="_blank">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100" id="submit-button">
                            Subscribe Now - ${{ number_format($plan->price, 2) }}/month
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe("{{ env('STRIPE_KEY') }}");
    const elements = stripe.elements();
    const card = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
                '::placeholder': {
                    color: '#aab7c4'
                }
            }
        }
    });
    card.mount('#card-element');
    
    const form = document.getElementById('subscription-form');
    const submitButton = document.getElementById('submit-button');
    const termsCheckbox = document.getElementById('terms');
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        if (!termsCheckbox.checked) {
            alert('Please agree to the Terms of Service and Privacy Policy');
            return;
        }
        
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';
        
        const {setupIntent, error} = await stripe.confirmCardSetup(
            "{{ $intent->client_secret }}",
            {
                payment_method: {
                    card: card,
                    billing_details: {
                        name: "{{ auth()->user()->name }}",
                        email: "{{ auth()->user()->email }}"
                    }
                }
            }
        );
        
        if (error) {
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
            submitButton.disabled = false;
            submitButton.textContent = 'Subscribe Now - ${{ number_format($plan->price, 2) }}/month';
        } else {
            document.getElementById('payment_method').value = setupIntent.payment_method;
            form.submit();
        }
    });
</script>
@endsection