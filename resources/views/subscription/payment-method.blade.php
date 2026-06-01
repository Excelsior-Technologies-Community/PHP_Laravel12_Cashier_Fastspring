{{-- resources/views/subscription/payment-method.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Update Payment Method</h4>
                </div>
                <div class="card-body">
                    <form id="payment-form" method="POST" action="{{ route('subscription.payment-method.update') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Card Details</label>
                            <div id="card-element" class="form-control"></div>
                            <div id="card-errors" class="text-danger mt-2" role="alert"></div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="submit-button">Update Payment Method</button>
                        <a href="{{ route('subscription.current') }}" class="btn btn-secondary">Cancel</a>
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
    
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';
        
        const {setupIntent, error} = await stripe.confirmCardSetup(
            "{{ $intent->client_secret }}",
            {
                payment_method: {
                    card: card
                }
            }
        );
        
        if (error) {
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
            submitButton.disabled = false;
            submitButton.textContent = 'Update Payment Method';
        } else {
            const hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'payment_method');
            hiddenInput.setAttribute('value', setupIntent.payment_method);
            form.appendChild(hiddenInput);
            form.submit();
        }
    });
</script>
@endsection