# Payment API Integration Guide

This document outlines the steps needed to integrate the payment API with the actual appointment module and payment gateways.

## Current State (Testing Mode)

The payment API is currently configured for testing with:
- ✅ Mock appointment data
- ✅ Simulated payment processing
- ✅ Database payment records
- ✅ Realistic test scenarios

## Integration Steps

### Step 1: Appointment Module Integration

#### In `PaymentController.php`, uncomment and modify:

```php
// Change this line:
$appointmentData = $this->getMockAppointmentData($request->appointment_id);

// To this:
$appointmentData = $this->appointmentService->getAppointmentData($request->appointment_id);
```

#### Update validation:
```php
// Uncomment this validation:
$request->validate([
    'appointment_id' => 'required|integer|exists:appointments,id',
    'payment_method' => 'required|string|in:credit_card,paypal,bank_transfer',
    'payment_details' => 'required|array'
]);

// Remove the temporary validation
```

#### Uncomment appointment status update:
```php
// Uncomment this after successful payment:
$this->appointmentService->updateAppointmentStatus(
    $request->appointment_id, 
    'paid'
);
```

### Step 2: Payment Gateway Integration

#### Replace simulation with real payment processing:

```php
// Replace this line:
$paymentResult = $this->simulatePaymentProcessing($request->payment_method, $request->payment_details, $amount);

// With this:
$paymentContext = $this->createPaymentContext($request->payment_method);
$paymentResult = $paymentContext->processPayment([
    'appointment_id' => $request->appointment_id,
    'amount' => $appointmentData['total_amount'],
    'currency' => $appointmentData['currency'] ?? 'USD',
    'payment_details' => $request->payment_details,
    'appointment_data' => $appointmentData
]);
```

### Step 3: Remove Temporary Methods

After integration, remove these temporary methods from `PaymentController.php`:
- `getMockAppointmentData()`
- `simulatePaymentProcessing()`
- `simulateCreditCardPayment()`
- `simulatePayPalPayment()`
- `simulateBankTransferPayment()`
- `getCardType()`

### Step 4: Environment Configuration

Update your `.env` file with real payment gateway credentials:

```env
# Payment Gateway Configuration
STRIPE_PUBLIC_KEY=pk_live_...
STRIPE_SECRET_KEY=sk_live_...
PAYPAL_CLIENT_ID=...
PAYPAL_CLIENT_SECRET=...
PAYPAL_MODE=live  # or sandbox for testing

# Appointment Module Configuration
APPOINTMENT_SERVICE_URL=https://your-appointment-service.com/api
APPOINTMENT_SERVICE_TOKEN=...
```

### Step 5: Update Payment Strategies

Ensure your payment strategy classes are configured with real gateway credentials:

#### `CreditCardPaymentStrategy.php`
```php
private function processStripePayment($paymentData) {
    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
    // Real Stripe integration code
}
```

#### `PayPalPaymentStrategy.php`
```php
private function processPayPalPayment($paymentData) {
    // Real PayPal SDK integration code
}
```

## Testing After Integration

### Test with Real Appointments
1. Create actual appointments in your appointment system
2. Use real appointment IDs in payment requests
3. Verify payment status updates appointment status

### Test with Sandbox Gateways
1. Use sandbox/test credentials for payment gateways
2. Test with real payment gateway test cards
3. Verify webhooks and callbacks work correctly

### Production Deployment
1. Switch to live payment gateway credentials
2. Test with small real transactions
3. Monitor payment success rates and errors

## Files to Modify

| File | Action | Description |
|------|--------|-------------|
| `PaymentController.php` | Uncomment/Modify | Restore real API calls |
| `.env` | Add | Payment gateway credentials |
| `AppointmentService.php` | Verify | Ensure methods exist |
| Payment Strategies | Update | Add real gateway integration |
| Tests | Update | Test with real appointment data |

## Rollback Plan

If issues occur during integration:

1. **Restore testing mode** by re-commenting the original code
2. **Use mock data** until issues are resolved
3. **Test thoroughly** before re-attempting integration

## Migration Checklist

- [ ] Appointment module is deployed and accessible
- [ ] Payment gateway accounts are set up
- [ ] Sandbox testing is complete
- [ ] Database migrations are applied
- [ ] Environment variables are configured
- [ ] Payment webhooks are configured
- [ ] Error handling is tested
- [ ] Monitoring and logging are in place
- [ ] Rollback plan is prepared

## Support

For integration support:
1. Check the payment gateway documentation
2. Verify appointment service API documentation
3. Test individual components separately
4. Use logging to debug integration issues

## Notes

- Keep the mock methods until integration is fully tested
- Consider feature flags to switch between mock and real processing
- Monitor payment success rates after going live
- Have rollback procedures ready for production deployment
