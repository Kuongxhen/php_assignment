# API Testing Guide

This document provides comprehensive information about testing the REST API endpoints in this Laravel application.

## Base URL
```
http://localhost/myproject/public/api/v1
```

## API Endpoints Overview

### Product API Endpoints
- `GET /products` - Get all products
- `GET /products/{id}` - Get a specific product
- `GET /products/category/{category}` - Get products by category
- `POST /products` - Create a new product
- `PUT /products/{id}` - Update a product
- `DELETE /products/{id}` - Delete a product
- `POST /products/check-availability` - Check product availability

### Payment API Endpoints
- `GET /payments` - Get all payments
- `GET /payments/{id}` - Get a specific payment
- `GET /payments/methods` - Get available payment methods
- `POST /payments/process` - Process a payment
- `GET /payments/{paymentId}/status` - Get payment status
- `GET /payments/appointment/{appointmentId}` - Get payments by appointment
- `POST /payments/{paymentId}/refund` - Refund a payment

### User API Endpoints
- `GET /users` - Get all users
- `GET /users/{id}` - Get a specific user
- `POST /users` - Create a new user
- `PUT /users/{id}` - Update a user
- `DELETE /users/{id}` - Delete a user

### System Endpoints
- `GET /health` - Health check

## Testing with cURL

### Product API Examples

#### Get All Products
```bash
curl -X GET "http://localhost/myproject/public/api/v1/products" \
  -H "Accept: application/json"
```

#### Create a Product
```bash
curl -X POST "http://localhost/myproject/public/api/v1/products" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "sku": "MED001",
    "name": "Paracetamol 500mg",
    "description": "Pain relief medication",
    "category": "Medicine",
    "price": 15.99,
    "cost": 8.50,
    "quantity": 100,
    "unit": "tablets",
    "manufacturer": "PharmaCorp",
    "expiration_date": "2025-12-31",
    "is_active": true
  }'
```

#### Check Product Availability
```bash
curl -X POST "http://localhost/myproject/public/api/v1/products/check-availability" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "product_ids": [1, 2],
    "quantities": [2, 1]
  }'
```

### Payment API Examples

#### Get Payment Methods
```bash
curl -X GET "http://localhost/myproject/public/api/v1/payments/methods" \
  -H "Accept: application/json"
```

#### Process Payment
```bash
curl -X POST "http://localhost/myproject/public/api/v1/payments/process" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "appointment_id": 123,
    "payment_method": "credit_card",
    "payment_details": {
      "card_number": "4111111111111111",
      "expiry_date": "12/26",
      "cvv": "123",
      "cardholder_name": "John Doe"
    }
  }'
```

### User API Examples

#### Create a User
```bash
curl -X POST "http://localhost/myproject/public/api/v1/users" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

#### Update a User
```bash
curl -X PUT "http://localhost/myproject/public/api/v1/users/1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Updated Doe",
    "email": "john.updated@example.com"
  }'
```

## Testing with Postman

### Import Collection
1. Open Postman
2. Click "Import"
3. Use the JSON files in `tests/api-test-data/` directory:
   - `products-api-test.json`
   - `payments-api-test.json`
   - `users-api-test.json`

### Environment Variables
Set up the following environment variables in Postman:
- `base_url`: `http://localhost/myproject/public/api/v1`
- `product_id`: `1` (or any valid product ID)
- `user_id`: `1` (or any valid user ID)
- `payment_id`: `1` (or any valid payment ID)

## Error Responses

### Common Error Formats

#### Validation Error (422)
```json
{
  "message": "The name field is required. (and 2 more errors)",
  "errors": {
    "name": ["The name field is required."],
    "email": ["The email field must be a valid email address."],
    "price": ["The price field must be a number."]
  }
}
```

#### Not Found Error (404)
```json
{
  "success": false,
  "message": "Product not found"
}
```

#### Server Error (500)
```json
{
  "success": false,
  "message": "Failed to create product",
  "error": "Database connection failed"
}
```

## Response Format

### Success Response
```json
{
  "success": true,
  "data": { ... },
  "message": "Operation completed successfully"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "error": "Detailed error information (optional)"
}
```

## Testing Checklist

### Product API
- [ ] Get all products
- [ ] Get single product by ID
- [ ] Get products by category
- [ ] Create new product with valid data
- [ ] Create product with invalid data (validation)
- [ ] Update existing product
- [ ] Delete product
- [ ] Check product availability

### Payment API
- [ ] Get all payments
- [ ] Get single payment by ID
- [ ] Get payment methods
- [ ] Process payment with credit card
- [ ] Process payment with PayPal
- [ ] Process payment with bank transfer
- [ ] Get payment status
- [ ] Get payments by appointment
- [ ] Process refund

### User API
- [ ] Get all users
- [ ] Get single user by ID
- [ ] Create new user with valid data
- [ ] Create user with invalid data (validation)
- [ ] Update existing user
- [ ] Delete user

### System
- [ ] Health check endpoint
- [ ] API versioning
- [ ] Error handling
- [ ] Response format consistency

## Notes

1. Make sure the Laravel development server is running
2. Ensure the database is properly seeded with test data
3. All API endpoints return JSON responses
4. Use proper HTTP status codes
5. Include appropriate headers in requests
6. Test both success and error scenarios
