<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        Payment Gateway
                    </h2>
                    <p class="mb-0 mt-2 opacity-75">Secure payment processing</p>
                </div>
                
                <div class="card-body p-4">
                    <!-- Payment Summary -->
                    <div class="payment-summary mb-4 p-3 bg-light rounded">
                        <h5 class="mb-3">
                            <i class="fas fa-receipt me-2"></i>
                            Payment Summary
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Appointment ID:</strong> <span id="appointment-id">#12345</span></p>
                                <p class="mb-1"><strong>Patient:</strong> <span id="patient-name">John Doe</span></p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="mb-1"><strong>Total Amount:</strong></p>
                                <h4 class="text-primary mb-0" id="total-amount">MYR 150.00</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="payment-methods mb-4">
                        <h5 class="mb-3">
                            <i class="fas fa-wallet me-2"></i>
                            Select Payment Method
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="payment-method-card" data-method="credit_card">
                                    <div class="card h-100 border-2 payment-option">
                                        <div class="card-body text-center p-3">
                                            <i class="fas fa-credit-card fa-2x text-primary mb-2"></i>
                                            <h6 class="card-title">Credit Card</h6>
                                            <small class="text-muted">Visa, MasterCard, Amex</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="payment-method-card" data-method="paypal">
                                    <div class="card h-100 border-2 payment-option">
                                        <div class="card-body text-center p-3">
                                            <i class="fab fa-paypal fa-2x text-info mb-2"></i>
                                            <h6 class="card-title">PayPal</h6>
                                            <small class="text-muted">Pay with PayPal</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="payment-method-card" data-method="bank_transfer">
                                    <div class="card h-100 border-2 payment-option">
                                        <div class="card-body text-center p-3">
                                            <i class="fas fa-university fa-2x text-success mb-2"></i>
                                            <h6 class="card-title">Bank Transfer</h6>
                                            <small class="text-muted">Direct bank transfer</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Forms -->
                    <form id="payment-form" class="payment-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="appointment_id" id="appointment_id" value="">
                        <input type="hidden" name="payment_method" id="payment_method" value="">

                        <!-- Credit Card Form -->
                        <div id="credit-card-form" class="payment-form-content" style="display: none;">
                            <h5 class="mb-3">
                                <i class="fas fa-credit-card me-2"></i>
                                Credit Card Information
                            </h5>
                            
                            <!-- Interactive Credit Card -->
                            <div class="credit-card-container mb-4">
                                <div class="credit-card" id="credit-card">
                                    <div class="credit-card-inner">
                                        <div class="credit-card-front">
                                            <div class="credit-card-logo">
                                                <i class="fab fa-cc-visa"></i>
                                            </div>
                                            <div class="credit-card-number">
                                                <span id="card-number-display">•••• •••• •••• ••••</span>
                                            </div>
                                            <div class="credit-card-info">
                                                <div class="credit-card-holder">
                                                    <label>CARD HOLDER</label>
                                                    <span id="card-holder-display">YOUR NAME</span>
                                                </div>
                                                <div class="credit-card-expiry">
                                                    <label>EXPIRES</label>
                                                    <span id="card-expiry-display">MM/YY</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="card_number" class="form-label">Card Number</label>
                                    <input type="text" class="form-control" id="card_number" name="card_number" 
                                           placeholder="1234 5678 9012 3456" maxlength="19">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-12">
                                    <label for="cardholder_name" class="form-label">Cardholder Name</label>
                                    <input type="text" class="form-control" id="cardholder_name" name="cardholder_name" 
                                           placeholder="John Doe">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="expiry_date" class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" id="expiry_date" name="expiry_date" 
                                           placeholder="MM/YY" maxlength="5">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv" name="cvv" 
                                           placeholder="123" maxlength="4">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <!-- PayPal Form -->
                        <div id="paypal-form" class="payment-form-content" style="display: none;">
                            <h5 class="mb-3">
                                <i class="fab fa-paypal me-2"></i>
                                PayPal Information
                            </h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="paypal_email" class="form-label">PayPal Email</label>
                                    <input type="email" class="form-control" id="paypal_email" name="paypal_email" 
                                           placeholder="your.email@example.com">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transfer Form -->
                        <div id="bank-transfer-form" class="payment-form-content" style="display: none;">
                            <h5 class="mb-3">
                                <i class="fas fa-university me-2"></i>
                                Bank Transfer Information
                            </h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="account_holder_name" class="form-label">Account Holder Name</label>
                                    <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" 
                                           placeholder="John Doe">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="account_number" class="form-label">Account Number</label>
                                    <input type="text" class="form-control" id="account_number" name="account_number" 
                                           placeholder="1234567890">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="routing_number" class="form-label">Routing Number</label>
                                    <input type="text" class="form-control" id="routing_number" name="routing_number" 
                                           placeholder="123456789">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="submit-payment" disabled>
                                <i class="fas fa-lock me-2"></i>
                                <span id="submit-text">Select Payment Method</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5>Processing Payment...</h5>
                <p class="text-muted mb-0">Please wait while we process your payment securely.</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
/* Payment Method Cards */
.payment-option {
    cursor: pointer;
    transition: all 0.3s ease;
    border-color: #e9ecef !important;
}

.payment-option:hover {
    border-color: #007bff !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
}

.payment-option.selected {
    border-color: #007bff !important;
    background-color: #f8f9ff;
}

/* Credit Card Styles */
.credit-card-container {
    perspective: 1000px;
    height: 200px;
}

.credit-card {
    width: 100%;
    max-width: 350px;
    height: 200px;
    position: relative;
    transform-style: preserve-3d;
    transition: transform 0.6s;
    margin: 0 auto;
}

.credit-card:hover {
    transform: rotateY(5deg);
}

.credit-card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    text-align: left;
    transition: transform 0.6s;
    transform-style: preserve-3d;
}

.credit-card-front {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 20px;
    color: white;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.credit-card-logo {
    text-align: right;
    font-size: 2rem;
    opacity: 0.8;
}

.credit-card-number {
    font-size: 1.4rem;
    font-weight: 500;
    letter-spacing: 2px;
    font-family: 'Courier New', monospace;
    margin: 20px 0;
}

.credit-card-info {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
}

.credit-card-holder,
.credit-card-expiry {
    display: flex;
    flex-direction: column;
}

.credit-card-holder label,
.credit-card-expiry label {
    font-size: 0.7rem;
    opacity: 0.8;
    margin-bottom: 5px;
}

.credit-card-holder span,
.credit-card-expiry span {
    font-size: 0.9rem;
    font-weight: 500;
}

/* Form Styles */
.payment-form-content {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Loading Animation */
.spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Responsive */
@media (max-width: 768px) {
    .credit-card {
        max-width: 300px;
        height: 180px;
    }
    
    .credit-card-number {
        font-size: 1.2rem;
    }
    
    .credit-card-logo {
        font-size: 1.5rem;
    }
}

/* Payment Summary */
.payment-summary {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-left: 4px solid #007bff;
}

/* Button Styles */
.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    padding: 12px 24px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

.btn-primary:disabled {
    background: #6c757d;
    transform: none;
    box-shadow: none;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment method selection
    const paymentMethodCards = document.querySelectorAll('.payment-method-card');
    const paymentForms = document.querySelectorAll('.payment-form-content');
    const submitButton = document.getElementById('submit-payment');
    const submitText = document.getElementById('submit-text');
    const paymentMethodInput = document.getElementById('payment_method');

    // Credit card elements
    const creditCard = document.getElementById('credit-card');
    const cardNumberDisplay = document.getElementById('card-number-display');
    const cardHolderDisplay = document.getElementById('card-holder-display');
    const cardExpiryDisplay = document.getElementById('card-expiry-display');

    // Form inputs
    const cardNumberInput = document.getElementById('card_number');
    const cardHolderInput = document.getElementById('cardholder_name');
    const expiryInput = document.getElementById('expiry_date');
    const cvvInput = document.getElementById('cvv');

    // Payment method selection
    paymentMethodCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selected class from all cards
            document.querySelectorAll('.payment-option').forEach(option => {
                option.classList.remove('selected');
            });

            // Add selected class to clicked card
            this.querySelector('.payment-option').classList.add('selected');

            // Hide all forms
            paymentForms.forEach(form => {
                form.style.display = 'none';
            });

            // Show selected form
            const method = this.dataset.method;
            const formId = method.replace('_', '-') + '-form';
            const selectedForm = document.getElementById(formId);
            if (selectedForm) {
                selectedForm.style.display = 'block';
            }

            // Update payment method and enable submit button
            paymentMethodInput.value = method;
            submitButton.disabled = false;
            submitText.textContent = `Pay ${getMethodDisplayName(method)}`;
        });
    });

    // Credit card number formatting and display
    cardNumberInput.addEventListener('input', function() {
        let value = this.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        
        if (formattedValue.length > 19) {
            formattedValue = formattedValue.substr(0, 19);
        }
        
        this.value = formattedValue;
        
        // Update card display
        const displayValue = formattedValue || '•••• •••• •••• ••••';
        cardNumberDisplay.textContent = displayValue;
        
        // Update card type
        updateCardType(value);
    });

    // Cardholder name formatting and display
    cardHolderInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
        cardHolderDisplay.textContent = this.value || 'YOUR NAME';
    });

    // Expiry date formatting and display
    expiryInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        this.value = value;
        cardExpiryDisplay.textContent = value || 'MM/YY';
    });

    // CVV input formatting
    cvvInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Form submission
    document.getElementById('payment-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            return;
        }

        // Show loading modal
        const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
        loadingModal.show();

        // Prepare payment data
        const formData = new FormData(this);
        const paymentData = {
            appointment_id: parseInt(formData.get('appointment_id')),
            payment_method: formData.get('payment_method'),
            payment_details: {}
        };

        // Add payment method specific details
        const method = paymentData.payment_method;
        if (method === 'credit_card') {
            paymentData.payment_details = {
                card_number: formData.get('card_number'),
                cardholder_name: formData.get('cardholder_name'),
                expiry_date: formData.get('expiry_date'),
                cvv: formData.get('cvv')
            };
        } else if (method === 'paypal') {
            paymentData.payment_details = {
                paypal_email: formData.get('paypal_email')
            };
        } else if (method === 'bank_transfer') {
            paymentData.payment_details = {
                account_number: formData.get('account_number'),
                routing_number: formData.get('routing_number'),
                account_holder_name: formData.get('account_holder_name')
            };
        }

        // Submit payment
        fetch('/api/payments/process', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(paymentData)
        })
        .then(response => response.json())
        .then(data => {
            loadingModal.hide();
            
            if (data.success) {
                // Redirect to success page
                window.location.href = `/payment/success?payment_id=${data.data.payment_id}`;
            } else {
                // Show error message
                showError(data.message || 'Payment failed. Please try again.');
            }
        })
        .catch(error => {
            loadingModal.hide();
            console.error('Error:', error);
            showError('An error occurred. Please try again.');
        });
    });

    // Helper functions
    function getMethodDisplayName(method) {
        const names = {
            'credit_card': 'with Credit Card',
            'paypal': 'with PayPal',
            'bank_transfer': 'via Bank Transfer'
        };
        return names[method] || method;
    }

    function updateCardType(number) {
        const cardTypes = {
            '4': 'visa',
            '5': 'mastercard',
            '3': 'amex'
        };
        
        const firstDigit = number.charAt(0);
        const cardType = cardTypes[firstDigit] || 'visa';
        
        // Update card logo
        const logo = creditCard.querySelector('.credit-card-logo i');
        logo.className = `fab fa-cc-${cardType}`;
    }

    function validateForm() {
        const method = paymentMethodInput.value;
        let isValid = true;

        // Clear previous validation
        document.querySelectorAll('.is-invalid').forEach(input => {
            input.classList.remove('is-invalid');
        });

        if (method === 'credit_card') {
            const requiredFields = ['card_number', 'cardholder_name', 'expiry_date', 'cvv'];
            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                }
            });

            // Validate card number
            const cardNumber = cardNumberInput.value.replace(/\s/g, '');
            if (cardNumber.length < 13 || cardNumber.length > 19) {
                cardNumberInput.classList.add('is-invalid');
                isValid = false;
            }

            // Validate expiry date
            const expiry = expiryInput.value;
            if (!/^\d{2}\/\d{2}$/.test(expiry)) {
                expiryInput.classList.add('is-invalid');
                isValid = false;
            }

            // Validate CVV
            const cvv = cvvInput.value;
            if (cvv.length < 3 || cvv.length > 4) {
                cvvInput.classList.add('is-invalid');
                isValid = false;
            }
        } else if (method === 'paypal') {
            const email = document.getElementById('paypal_email');
            if (!email.value.trim() || !isValidEmail(email.value)) {
                email.classList.add('is-invalid');
                isValid = false;
            }
        } else if (method === 'bank_transfer') {
            const requiredFields = ['account_number', 'routing_number', 'account_holder_name'];
            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                }
            });
        }

        return isValid;
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function showError(message) {
        // Create and show error alert
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.card-body');
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Initialize with sample data (replace with actual data from appointment)
    document.getElementById('appointment_id').value = '12345';
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/myproject/resources/views/payment/index.blade.php ENDPATH**/ ?>