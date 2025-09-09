# API Documentation

## Overview

This REST API provides endpoints for managing products, payments, and users in the Laravel application. All API endpoints are versioned and prefixed with `/api/v1`.

## Base URL
```
http://localhost/myproject/public/api/v1
```

## Authentication
Currently, the API does not require authentication. In a production environment, you should implement API authentication using Laravel Sanctum or Passport.

## Response Format

All API responses follow a consistent JSON format:

### Success Response
```json
{
  "success": true,
  "data": { ... },
  "message": "Descriptive success message"
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

## HTTP Status Codes

- `200` - OK (successful GET, PUT, DELETE)
- `201` - Created (successful POST)
- `400` - Bad Request (invalid data)
- `404` - Not Found (resource doesn't exist)
- `422` - Unprocessable Entity (validation errors)
- `500` - Internal Server Error

## API Endpoints

### Products

#### Get All Products
- **URL:** `GET /products`
- **Description:** Retrieve all active products with available quantity
- **Response:** Array of product objects

#### Get Product by ID
- **URL:** `GET /products/{id}`
- **Description:** Retrieve a specific product by ID
- **Parameters:** 
  - `id` (integer) - Product ID
- **Response:** Single product object

#### Get Products by Category
- **URL:** `GET /products/category/{category}`
- **Description:** Retrieve products filtered by category
- **Parameters:**
  - `category` (string) - Product category
- **Response:** Array of product objects

#### Create Product
- **URL:** `POST /products`
- **Description:** Create a new product
- **Request Body:**
```json
{
  "sku": "string (required, unique)",
  "name": "string (required)",
  "description": "string (optional)",
  "category": "string (required)",
  "price": "number (required, min: 0)",
  "cost": "number (optional, min: 0)",
  "quantity": "integer (required, min: 0)",
  "unit": "string (required)",
  "manufacturer": "string (optional)",
  "expiration_date": "date (optional)",
  "is_active": "boolean (optional, default: true)"
}
```

#### Update Product
- **URL:** `PUT /products/{id}`
- **Description:** Update an existing product
- **Parameters:**
  - `id` (integer) - Product ID
- **Request Body:** Same as create, but all fields are optional

#### Delete Product
- **URL:** `DELETE /products/{id}`
- **Description:** Delete a product
- **Parameters:**
  - `id` (integer) - Product ID

#### Check Product Availability
- **URL:** `POST /products/check-availability`
- **Description:** Check availability and calculate total for multiple products
- **Request Body:**
```json
{
  "product_ids": [1, 2, 3],
  "quantities": [2, 1, 3]
}
```

### Payments

#### Get All Payments
- **URL:** `GET /payments`
- **Description:** Retrieve all payments
- **Response:** Array of payment objects

#### Get Payment by ID
- **URL:** `GET /payments/{id}`
- **Description:** Retrieve a specific payment by ID
- **Parameters:**
  - `id` (integer) - Payment ID
- **Response:** Single payment object

#### Get Payment Methods
- **URL:** `GET /payments/methods`
- **Description:** Get available payment methods and their requirements
- **Response:** Object with payment method details

#### Process Payment
- **URL:** `POST /payments/process`
- **Description:** Process a payment for an appointment
- **Request Body:**
```json
{
  "appointment_id": "integer (required)",
  "payment_method": "string (required, values: credit_card|paypal|bank_transfer)",
  "payment_details": {
    // Payment method specific details
  }
}
```

#### Get Payment Status
- **URL:** `GET /payments/{paymentId}/status`
- **Description:** Get the current status of a payment
- **Parameters:**
  - `paymentId` (integer) - Payment ID

#### Get Payments by Appointment
- **URL:** `GET /payments/appointment/{appointmentId}`
- **Description:** Get all payments for a specific appointment
- **Parameters:**
  - `appointmentId` (integer) - Appointment ID

#### Refund Payment
- **URL:** `POST /payments/{paymentId}/refund`
- **Description:** Process a refund for a payment
- **Parameters:**
  - `paymentId` (integer) - Payment ID
- **Request Body:**
```json
{
  "reason": "string (required)",
  "amount": "number (optional, defaults to full amount)"
}
```

### Users

#### Get All Users
- **URL:** `GET /users`
- **Description:** Retrieve all users
- **Response:** Array of user objects

#### Get User by ID
- **URL:** `GET /users/{id}`
- **Description:** Retrieve a specific user by ID
- **Parameters:**
  - `id` (integer) - User ID
- **Response:** Single user object

#### Create User
- **URL:** `POST /users`
- **Description:** Create a new user
- **Request Body:**
```json
{
  "name": "string (required)",
  "email": "string (required, unique, valid email)",
  "password": "string (required, min: 8 characters)",
  "password_confirmation": "string (required, must match password)"
}
```

#### Update User
- **URL:** `PUT /users/{id}`
- **Description:** Update an existing user
- **Parameters:**
  - `id` (integer) - User ID
- **Request Body:**
```json
{
  "name": "string (optional)",
  "email": "string (optional, unique, valid email)",
  "password": "string (optional, min: 8 characters)",
  "password_confirmation": "string (required if password provided)"
}
```

#### Delete User
- **URL:** `DELETE /users/{id}`
- **Description:** Delete a user
- **Parameters:**
  - `id` (integer) - User ID

### System

#### Health Check
- **URL:** `GET /health`
- **Description:** Check API health status
- **Response:**
```json
{
  "status": "ok",
  "timestamp": "2025-09-08T12:00:00Z",
  "version": "1.0.0"
}
```

## Error Handling

### Validation Errors (422)
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

### Not Found Errors (404)
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### Server Errors (500)
```json
{
  "success": false,
  "message": "Internal server error",
  "error": "Detailed error message (in development mode)"
}
```

## Rate Limiting

Currently, no rate limiting is implemented. In production, consider implementing rate limiting to prevent abuse.

## CORS

Cross-Origin Resource Sharing (CORS) is not configured. If you need to access the API from a different domain, configure CORS in Laravel.

## Security Recommendations

1. Implement API authentication (Laravel Sanctum/Passport)
2. Add rate limiting
3. Configure CORS properly
4. Use HTTPS in production
5. Validate and sanitize all input data
6. Implement proper error logging
