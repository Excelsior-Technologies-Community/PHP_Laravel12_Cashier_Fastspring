@extends('layouts.app')

@section('content')
<div class="container">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h2>Available Plans</h2>
    @foreach($plans as $plan)
        <div class="card mb-3">
            <div class="card-body">
                <h5>{{ $plan->name }}</h5>
                <p>Price: ${{ number_format($plan->price, 2) }}</p>
                <a href="{{ route('plans.show', $plan->slug) }}" class="btn btn-primary">Subscribe</a>
            </div>
        </div>
    @endforeach
</div>
@endsection
