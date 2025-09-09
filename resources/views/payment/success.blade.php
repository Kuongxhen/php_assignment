@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <!-- Success Icon -->
                    <div class="success-icon mb-4">
                        <div class="checkmark-circle">
                            <div class="checkmark"></div>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <h2 class="text-success mb-3">Payment Successful!</h2>
                    <p class="text-muted mb-4">Your payment has been processed successfully. You will receive a confirmation email shortly.</p>

                    <!-- Payment Details -->
                    <div class="payment-details bg-light rounded p-4 mb-4">
                        <h5 class="mb-3">
                            <i class="fas fa-receipt me-2"></i>
                            Payment Details
                        </h5>
                        <div class="row text-start">
                            <div class="col-6">
                                <p class="mb-2"><strong>Payment ID:</strong></p>
                                <p class="mb-2"><strong>Transaction ID:</strong></p>
                                <p class="mb-2"><strong>Amount:</strong></p>
                                <p class="mb-2"><strong>Method:</strong></p>
                                <p class="mb-0"><strong>Date:</strong></p>
                            </div>
                            <div class="col-6">
                                <p class="mb-2" id="payment-id">#{{ request('payment_id', 'N/A') }}</p>
                                <p class="mb-2" id="transaction-id">Loading...</p>
                                <p class="mb-2" id="amount">Loading...</p>
                                <p class="mb-2" id="method">Loading...</p>
                                <p class="mb-0" id="date">Loading...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>
                            Print Receipt
                        </button>
                        <a href="/" class="btn btn-primary">
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
/* Success Animation */
.success-icon {
    display: flex;
    justify-content: center;
    align-items: center;
}

.checkmark-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    animation: scaleIn 0.5s ease-out;
}

.checkmark {
    width: 30px;
    height: 30px;
    border: 3px solid white;
    border-top: none;
    border-right: none;
    transform: rotate(-45deg);
    animation: checkmark 0.5s ease-out 0.3s both;
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

@keyframes checkmark {
    0% {
        width: 0;
        height: 0;
        opacity: 0;
    }
    100% {
        width: 30px;
        height: 30px;
        opacity: 1;
    }
}

/* Payment Details */
.payment-details {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-left: 4px solid #28a745;
}

/* Print Styles */
@media print {
    .btn {
        display: none !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
    }
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
                    document.getElementById('transaction-id').textContent = data.data.transaction_reference || 'N/A';
                    document.getElementById('amount').textContent = `${data.data.currency} ${parseFloat(data.data.amount).toFixed(2)}`;
                    document.getElementById('method').textContent = data.data.method_display_name;
                    document.getElementById('date').textContent = new Date(data.data.paid_at).toLocaleString();
                }
            })
            .catch(error => {
                console.error('Error fetching payment details:', error);
                document.getElementById('transaction-id').textContent = 'N/A';
                document.getElementById('amount').textContent = 'N/A';
                document.getElementById('method').textContent = 'N/A';
                document.getElementById('date').textContent = 'N/A';
            });
    }
});
</script>
@endpush
