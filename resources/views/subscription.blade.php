@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Subscribe to {{ $plan->name }}</h2>
    <p>Price: ${{ number_format($plan->price, 2) }}</p>

    <form id="subscription-form" method="POST" action="{{ route('subscription.create') }}">
        @csrf
        <input type="hidden" name="plan" value="{{ $plan->id }}">
        <input type="hidden" name="payment_method" id="payment_method">

        <div id="card-element"><!-- Stripe Card Element --></div>

        <button type="submit" class="btn btn-primary mt-3">Purchase</button>
    </form>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe("{{ env('STRIPE_KEY') }}");
const elements = stripe.elements();
const card = elements.create('card');
card.mount('#card-element');

const form = document.getElementById('subscription-form');

form.addEventListener('submit', async (e) => {
    e.preventDefault(); // Prevent page refresh

    // Confirm the card setup
    const {setupIntent, error} = await stripe.confirmCardSetup(
        "{{ $intent->client_secret }}",
        { payment_method: { card: card } }
    );

    if(error){
        alert(error.message); // Show Stripe errors
    } else {
        // Set the payment method token in hidden input
        document.getElementById('payment_method').value = setupIntent.payment_method;
        form.submit(); // Submit the form to Laravel controller
    }
});
</script>
@endsection
