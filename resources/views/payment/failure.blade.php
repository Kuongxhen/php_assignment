@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <!-- Failure Icon -->
                    <div class="failure-icon mb-4">
                        <div class="error-circle">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>

                    <!-- Failure Message -->
                    <h2 class="text-danger mb-3">Payment Failed</h2>
                    <p class="text-muted mb-4">We're sorry, but your payment could not be processed. Please try again or use a different payment method.</p>

                    <!-- Error Details -->
                    <div class="error-details bg-light rounded p-4 mb-4">
                        <h5 class="mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error Details
                        </h5>
                        <div class="row text-start">
                            <div class="col-6">
                                <p class="mb-2"><strong>Payment ID:</strong></p>
                                <p class="mb-2"><strong>Error:</strong></p>
                                <p class="mb-2"><strong>Method:</strong></p>
                                <p class="mb-0"><strong>Date:</strong></p>
                            </div>
                            <div class="col-6">
                                <p class="mb-2" id="payment-id">#{{ request('payment_id', 'N/A') }}</p>
                                <p class="mb-2" id="error-message">Loading...</p>
                                <p class="mb-2" id="method">Loading...</p>
                                <p class="mb-0" id="date">Loading...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Help Section -->
                    <div class="help-section bg-info bg-opacity-10 rounded p-4 mb-4">
                        <h6 class="text-info mb-3">
                            <i class="fas fa-question-circle me-2"></i>
                            Need Help?
                        </h6>
                        <div class="row text-start">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <i class="fas fa-credit-card text-primary me-2"></i>
                                    Check your card details
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-university text-success me-2"></i>
                                    Verify account information
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <i class="fas fa-phone text-info me-2"></i>
                                    Contact support
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-envelope text-warning me-2"></i>
                                    Try different method
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="/payment" class="btn btn-primary">
                            <i class="fas fa-redo me-2"></i>
                            Try Again
                        </a>
                        <a href="/" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>
                            Return Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Failure Animation */
.failure-icon {
    display: flex;
    justify-content: center;
    align-items: center;
}

.error-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    animation: scaleIn 0.5s ease-out;
}

.error-circle i {
    color: white;
    font-size: 2rem;
    animation: shake 0.5s ease-out 0.3s both;
}

@keyframes scaleIn {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes shake {
    0%, 100% {
        transform: translateX(0);
    }
    25% {
        transform: translateX(-5px);
    }
    75% {
        transform: translateX(5px);
    }
}

/* Error Details */
.error-details {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-left: 4px solid #dc3545;
}

/* Help Section */
.help-section {
    border-left: 4px solid #17a2b8;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentId = '{{ request("payment_id") }}';
    
    if (paymentId && paymentId !== 'N/A') {
        // Fetch payment details
        fetch(`/api/payments/${paymentId}/status`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('error-message').textContent = data.data.failure_reason || 'Unknown error';
                    document.getElementById('method').textContent = data.data.method_display_name;
                    document.getElementById('date').textContent = new Date(data.data.created_at).toLocaleString();
                }
            })
            .catch(error => {
                console.error('Error fetching payment details:', error);
                document.getElementById('error-message').textContent = 'Unable to load error details';
                document.getElementById('method').textContent = 'N/A';
                document.getElementById('date').textContent = 'N/A';
            });
    }
});
</script>
@endpush
