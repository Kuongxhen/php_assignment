# Payment API Testing Guide

This guide provides detailed instructions for testing the payment processing functionality with realistic scenarios and mock data.

## Overview

The payment API now uses mock appointment data and simulated payment processing to avoid external dependencies while providing realistic testing scenarios.

## Mock Appointment Data

The following test appointments are available:

| Appointment ID | Patient Name | Date | Amount | Status |
|----------------|--------------|------|--------|---------|
| 123 | John Doe | 2025-09-15 | $75.50 | confirmed |
| 124 | Jane Smith | 2025-09-16 | $150.00 | confirmed |
| 125 | Bob Johnson | 2025-09-17 | $80.00 | pending |

## Test Scenarios

### Credit Card Testing

#### ✅ Successful Payment
```bash
curl -X POST "http://localhost/myproject/public/api/v1/payments/process" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "appointment_id": 123,
    "payment_method": "credit_card",
    "amount": 75.50,
    "payment_details": {
      "card_number": "4111111111111111",
      "expiry_date": "12/26",
      "cvv": "123",
      "cardholder_name": "John Doe"
    }
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "payment_id": 1,
    "transaction_id": "txn_...",
    "status": "completed",
    "amount": 75.50,
    "currency": "USD",
    "payment_method": "credit_card",
    "processed_at": "2025-09-08T...",
    "details": {
      "card_last_four": "1111",
      "card_type": "visa",
      "authorization_code": "AUTH_...",
      "processor_response": "APPROVED"
    }
  },
  "message": "Payment processed successfully"
}
```

#### ❌ Card Declined
```bash
curl -X POST "http://localhost/myproject/public/api/v1/payments/process" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "appointment_id": 123,
    "payment_method": "credit_card",
    "payment_details": {
      "card_number": "4000000000000002",
      "expiry_date": "12/26",
      "cvv": "123",
      "cardholder_name": "John Doe"
    }
  }'
```

**Expected Response:**
```json
{
  "success": false,
  "message": "Card declined by issuer",
  "data": {
    "payment_id": 1,
    "status": "failed",
    "error_code": "CARD_DECLINED"
  }
}
```

#### ❌ Insufficient Funds
```bash
curl -X POST "http://localhost/myproject/public/api/v1/payments/process" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "appointment_id": 123,
    "payment_method": "credit_card",
    "payment_details": {
      "card_number": "4000000000000127",
      "expiry_date": "12/26",
      "cvv": "123",
      "cardholder_name": "John Doe"
    }
  }'
```

### PayPal Testing

#### ✅ Successful PayPal Payment
```bash
curl -X POST "http://localhost/myproject/public/api/v1/payments/process" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "appointment_id": 124,
    "payment_method": "paypal",
    "payment_details": {
      "email": "user@example.com"
    }
  }'
```

#### ❌ PayPal Declined
```bash
curl -X POST "http://localhost/myproject/public/api/v1/payments/process" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "appointment_id": 124,
    "payment_method": "paypal",
    "payment_details": {
      "email": "declined@example.com"
    }
  }'
```

### Bank Transfer Testing

#### ✅ Successful Bank Transfer
```bash
curl -X POST "http://localhost/myproject/public/api/v1/payments/process" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "appointment_id": 125,
    "payment_method": "bank_transfer",
    "payment_details": {
      "account_number": "9876543210",
      "routing_number": "021000021",
      "account_holder_name": "Bob Johnson"
    }
  }'
```

#### ❌ Invalid Account
```bash
curl -X POST "http://localhost/myproject/public/api/v1/payments/process" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "appointment_id": 125,
    "payment_method": "bank_transfer",
    "payment_details": {
      "account_number": "1234567890",
      "routing_number": "021000021",
      "account_holder_name": "Bob Johnson"
    }
  }'
```

## Test Card Numbers

| Card Number | Type | Expected Result |
|-------------|------|-----------------|
| 4111111111111111 | Visa | Success |
| 4000000000000002 | Visa | Card Declined |
| 4000000000000127 | Visa | Insufficient Funds |
| 5555555555554444 | Mastercard | Success |
| 378282246310005 | Amex | Success |

## Test Email Addresses

| Email | Expected Result |
|-------|----------------|
| user@example.com | Success |
| declined@example.com | PayPal Declined |
| any-other-email@domain.com | Success |

## Test Bank Accounts

| Account Number | Expected Result |
|---------------|----------------|
| 9876543210 | Success |
| 1234567890 | Invalid Account |
| Any other number | Success |

## Error Testing

### Missing Required Fields
```bash
curl -X POST "http://localhost/myproject/public/api/v1/payments/process" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "appointment_id": 123,
    "payment_method": "credit_card",
    "payment_details": {
      "card_number": "4111111111111111"
    }
  }'
```

### Invalid Appointment ID
```bash
curl -X POST "http://localhost/myproject/public/api/v1/payments/process" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "appointment_id": 999,
    "payment_method": "credit_card",
    "payment_details": {
      "card_number": "4111111111111111",
      "expiry_date": "12/26",
      "cvv": "123",
      "cardholder_name": "John Doe"
    }
  }'
```

## Payment Status Checking

After processing a payment, you can check its status:

```bash
curl -X GET "http://localhost/myproject/public/api/v1/payments/1/status" \
  -H "Accept: application/json"
```

## Retrieving Payments

### Get All Payments
```bash
curl -X GET "http://localhost/myproject/public/api/v1/payments" \
  -H "Accept: application/json"
```

### Get Specific Payment
```bash
curl -X GET "http://localhost/myproject/public/api/v1/payments/1" \
  -H "Accept: application/json"
```

### Get Payments by Appointment
```bash
curl -X GET "http://localhost/myproject/public/api/v1/payments/appointment/123" \
  -H "Accept: application/json"
```

## Notes

1. **Processing Delay**: Each payment simulation includes a 0.5-second delay to simulate real processing time
2. **Unique Transaction IDs**: Each successful payment gets a unique transaction ID
3. **Payment Records**: All payment attempts (successful or failed) are saved to the database
4. **Mock Data**: Appointment data is simulated and doesn't require an actual appointment system
5. **No External Calls**: All payment processing is simulated locally

## Postman Testing

Import the `postman-collection.json` file which includes all these test scenarios with proper request bodies and expected responses.

## Troubleshooting

1. **Database Issues**: Ensure your database is properly configured and the payments table exists
2. **Route Issues**: Make sure you've run `php artisan route:cache` after updating routes
3. **JSON Responses**: Always include `Accept: application/json` header for proper JSON responses
