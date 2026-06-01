{{-- resources/views/subscription/current.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
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
            
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header">
                    <h4>Current Subscription</h4>
                </div>
                <div class="card-body">
                    @if($subscription && $subscription->active())
                        <div class="alert alert-success">
                            <strong>Status:</strong> Active
                        </div>
                        <table class="table">
                            <tr>
                                <th>Plan Name:</th>
                                <td>{{ $subscription->stripe_plan ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Started On:</th>
                                <td>{{ $subscription->created_at->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Next Billing Date:</th>
                                <td>{{ $subscription->asStripeSubscription()->current_period_end ? date('M d, Y', $subscription->asStripeSubscription()->current_period_end) : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Payment Method:</th>
                                <td>
                                    @php
                                        $paymentMethod = $user->defaultPaymentMethod();
                                    @endphp
                                    @if($paymentMethod)
                                        {{ ucfirst($paymentMethod->card->brand) }} ending in {{ $paymentMethod->card->last4 }}
                                        (Expires: {{ $paymentMethod->card->exp_month }}/{{ $paymentMethod->card->exp_year }})
                                    @else
                                        No payment method on file
                                    @endif
                                </td>
                            </tr>
                        </table>
                        
                        <div class="btn-group mt-3">
                            <a href="{{ route('subscription.change-plan.form') }}" class="btn btn-warning">Change Plan</a>
                            <a href="{{ route('subscription.payment-method.form') }}" class="btn btn-info">Update Payment Method</a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">Cancel Subscription</button>
                        </div>
                        
                        <!-- Cancel Modal -->
                        <div class="modal fade" id="cancelModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Cancel Subscription</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to cancel your subscription?</p>
                                        <p class="text-danger">You will lose access to premium features at the end of your billing period.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="{{ route('subscription.cancel') }}" method="POST">
                                            @csrf
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-danger">Yes, Cancel Subscription</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    @elseif($subscription && $subscription->onGracePeriod())
                        <div class="alert alert-warning">
                            <strong>Status:</strong> Cancelled (Access until {{ $subscription->ends_at->format('M d, Y') }})
                        </div>
                        <div class="mt-3">
                            <form action="{{ route('subscription.resume') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">Resume Subscription</button>
                            </form>
                        </div>
                        
                    @else
                        <div class="alert alert-info">
                            You don't have an active subscription. 
                            <a href="{{ route('plans.index') }}" class="alert-link">View Plans</a>
                        </div>
                    @endif
                </div>
            </div>
            
            @if($invoices->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h4>Payment History</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->date()->toFormattedDateString() }}</td>
                                    <td>{{ $invoice->total() }}</td>
                                    <td>
                                        @if($invoice->paid)
                                            <span class="badge bg-success">Paid</span>
                                        @else
                                            <span class="badge bg-danger">Unpaid</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('invoices.download', $invoice->id) }}" class="btn btn-sm btn-primary">
                                            Download PDF
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection