# API Documentation - Enhanced Version

## Overview

This REST API provides secure endpoints for managing products, payments, stock alerts, and reorder requests in the Laravel inventory management system. All API endpoints are versioned and prefixed with `/api/v1`.

## Base URL
```
http://localhost/myproject/public/api/v1
```

## Authentication

The API uses Laravel Sanctum for authentication. Most endpoints require authentication via Bearer token.

### Authentication Flow

1. Register or login to get an access token
2. Include the token in the Authorization header: `Authorization: Bearer {your-token}`
3. Use the token for all protected endpoints

### Public Endpoints (No Authentication Required)
- Health check
- Product browsing (read-only)
- Product availability check
- User registration/login

### Protected Endpoints (Authentication Required)
- All write operations (create, update, delete)
- Stock alerts management
- Reorder requests management
- User management (admin only)

## Authorization Roles

- **Admin**: Full access to all endpoints
- **Manager**: Access to inventory management, stock alerts, reorder requests
- **User**: Basic access (will be defined by user management team)

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
  "error": "Detailed error information",
  "errors": { ... } // For validation errors
}
```

## HTTP Status Codes

- `200` - OK (successful GET, PUT, DELETE)
- `201` - Created (successful POST)
- `400` - Bad Request (invalid data)
- `401` - Unauthorized (authentication required)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found (resource doesn't exist)
- `422` - Unprocessable Entity (validation errors)
- `429` - Too Many Requests (rate limit exceeded)
- `500` - Internal Server Error

## API Endpoints

### Authentication

#### Register User
- **URL:** `POST /auth/register`
- **Description:** Register a new user (handled by external team)
- **Public:** Yes
- **Request Body:**
```json
{
  "name": "string (required)",
  "email": "string (required, unique, valid email)",
  "password": "string (required, min: 8 characters)",
  "password_confirmation": "string (required, must match password)",
  "role": "string (optional, values: admin|manager|user, default: user)"
}
```

#### Login
- **URL:** `POST /auth/login`
- **Description:** Login and receive access token
- **Public:** Yes
- **Request Body:**
```json
{
  "email": "string (required)",
  "password": "string (required)"
}
```

#### Get User Profile
- **URL:** `GET /auth/me`
- **Description:** Get authenticated user details
- **Authentication:** Required

#### Logout
- **URL:** `POST /auth/logout`
- **Description:** Logout and revoke current token
- **Authentication:** Required

#### Revoke All Tokens
- **URL:** `POST /auth/revoke-all`
- **Description:** Revoke all tokens for the user
- **Authentication:** Required

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
- **Public:** Yes
- **Response:** Array of product objects

#### Get Product by ID
- **URL:** `GET /products/{id}`
- **Description:** Retrieve a specific product by ID
- **Public:** Yes
- **Parameters:** 
  - `id` (integer) - Product ID
- **Response:** Single product object

#### Get Products by Category
- **URL:** `GET /products/category/{category}`
- **Description:** Retrieve products filtered by category
- **Public:** Yes
- **Parameters:**
  - `category` (string) - Product category
- **Response:** Array of product objects

#### Check Product Availability
- **URL:** `POST /products/check-availability`
- **Description:** Check availability and calculate total for multiple products
- **Public:** Yes
- **Request Body:**
```json
{
  "product_ids": [1, 2, 3],
  "quantities": [2, 1, 3]
}
```

#### Create Product
- **URL:** `POST /products`
- **Description:** Create a new product
- **Authentication:** Required (Admin/Manager)
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
- **Authentication:** Required (Admin/Manager)
- **Parameters:**
  - `id` (integer) - Product ID
- **Request Body:** Same as create, but all fields are optional

#### Delete Product
- **URL:** `DELETE /products/{id}`
- **Description:** Delete a product
- **Authentication:** Required (Admin only)
- **Parameters:**
  - `id` (integer) - Product ID

### Stock Alerts

#### Get All Stock Alerts
- **URL:** `GET /stock-alerts`
- **Description:** Retrieve stock alerts with filtering options
- **Authentication:** Required (Admin/Manager)
- **Query Parameters:**
  - `status` (optional) - Filter by status: active, acknowledged, resolved
  - `severity` (optional) - Filter by severity: low, medium, high, critical
  - `alert_type` (optional) - Filter by type: low_stock, out_of_stock, expired

#### Get Stock Alert Statistics
- **URL:** `GET /stock-alerts/stats`
- **Description:** Get statistics about stock alerts
- **Authentication:** Required (Admin/Manager)

#### Create Stock Alert
- **URL:** `POST /stock-alerts`
- **Description:** Create a new stock alert
- **Authentication:** Required (Admin/Manager)
- **Request Body:**
```json
{
  "product_id": "integer (required)",
  "alert_type": "string (required, values: low_stock|out_of_stock|expired)",
  "message": "string (required, max: 500)",
  "current_quantity": "integer (required, min: 0)",
  "reorder_level": "integer (required, min: 0)",
  "severity": "string (required, values: low|medium|high|critical)"
}
```

#### Get Stock Alert by ID
- **URL:** `GET /stock-alerts/{id}`
- **Description:** Retrieve a specific stock alert
- **Authentication:** Required (Admin/Manager)
- **Parameters:**
  - `id` (integer) - Stock alert ID

#### Update Stock Alert
- **URL:** `PUT /stock-alerts/{id}`
- **Description:** Update a stock alert
- **Authentication:** Required (Admin/Manager)
- **Parameters:**
  - `id` (integer) - Stock alert ID
- **Request Body:**
```json
{
  "status": "string (optional, values: active|acknowledged|resolved)",
  "severity": "string (optional, values: low|medium|high|critical)",
  "message": "string (optional, max: 500)"
}
```

#### Acknowledge Stock Alert
- **URL:** `POST /stock-alerts/{id}/acknowledge`
- **Description:** Mark a stock alert as acknowledged
- **Authentication:** Required (Admin/Manager)
- **Parameters:**
  - `id` (integer) - Stock alert ID

#### Resolve Stock Alert
- **URL:** `POST /stock-alerts/{id}/resolve`
- **Description:** Mark a stock alert as resolved
- **Authentication:** Required (Admin/Manager)
- **Parameters:**
  - `id` (integer) - Stock alert ID

#### Trigger Stock Check
- **URL:** `POST /stock-alerts/trigger-check`
- **Description:** Manually trigger stock level check for all products
- **Authentication:** Required (Admin/Manager)

#### Delete Stock Alert
- **URL:** `DELETE /stock-alerts/{id}`
- **Description:** Delete a stock alert
- **Authentication:** Required (Admin only)
- **Parameters:**
  - `id` (integer) - Stock alert ID

### Reorder Requests

#### Get All Reorder Requests
- **URL:** `GET /reorder-requests`
- **Description:** Retrieve reorder requests with filtering options
- **Authentication:** Required (Admin/Manager)
- **Query Parameters:**
  - `status` (optional) - Filter by status: pending, approved, ordered, received, cancelled
  - `priority` (optional) - Filter by priority: low, medium, high, urgent

#### Get Reorder Request Statistics
- **URL:** `GET /reorder-requests/stats`
- **Description:** Get statistics about reorder requests
- **Authentication:** Required (Admin/Manager)

#### Create Reorder Request
- **URL:** `POST /reorder-requests`
- **Description:** Create a new reorder request
- **Authentication:** Required (Admin/Manager)
- **Request Body:**
```json
{
  "product_id": "integer (required)",
  "current_quantity": "integer (required, min: 0)",
  "reorder_level": "integer (required, min: 1)",
  "suggested_quantity": "integer (required, min: 1)",
  "priority": "string (required, values: low|medium|high|urgent)",
  "estimated_cost": "number (optional, min: 0)",
  "supplier": "string (optional, max: 255)",
  "notes": "string (optional, max: 1000)"
}
```

#### Get Reorder Request by ID
- **URL:** `GET /reorder-requests/{id}`
- **Description:** Retrieve a specific reorder request
- **Authentication:** Required (Admin/Manager)
- **Parameters:**
  - `id` (integer) - Reorder request ID

#### Update Reorder Request
- **URL:** `PUT /reorder-requests/{id}`
- **Description:** Update a reorder request
- **Authentication:** Required (Admin/Manager)
- **Parameters:**
  - `id` (integer) - Reorder request ID

#### Approve Reorder Request
- **URL:** `POST /reorder-requests/{id}/approve`
- **Description:** Approve a reorder request
- **Authentication:** Required (Admin/Manager)
- **Parameters:**
  - `id` (integer) - Reorder request ID

#### Cancel Reorder Request
- **URL:** `POST /reorder-requests/{id}/cancel`
- **Description:** Cancel a reorder request
- **Authentication:** Required (Admin/Manager)
- **Parameters:**
  - `id` (integer) - Reorder request ID

#### Mark as Ordered
- **URL:** `POST /reorder-requests/{id}/mark-ordered`
- **Description:** Mark reorder request as ordered
- **Authentication:** Required (Admin/Manager)
- **Parameters:**
  - `id` (integer) - Reorder request ID

#### Mark as Received
- **URL:** `POST /reorder-requests/{id}/mark-received`
- **Description:** Mark reorder request as received and update stock
- **Authentication:** Required (Admin/Manager)
- **Parameters:**
  - `id` (integer) - Reorder request ID

#### Delete Reorder Request
- **URL:** `DELETE /reorder-requests/{id}`
- **Description:** Delete a reorder request
- **Authentication:** Required (Admin only)
- **Parameters:**
  - `id` (integer) - Reorder request ID

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

API requests are rate limited to 60 requests per minute per user for authenticated endpoints.

## CORS

Cross-Origin Resource Sharing (CORS) is configured to allow API access from different domains. Current configuration allows all origins for development.

## Security Features

✅ **Implemented:**
1. Laravel Sanctum authentication with Bearer tokens
2. Role-based authorization (Admin/Manager/User)
3. Rate limiting (60 requests per minute)
4. Input sanitization middleware
5. Comprehensive validation with custom error messages
6. CORS configuration
7. Global exception handling for consistent error responses

## Security Best Practices

1. **Always use HTTPS in production**
2. **Store API tokens securely on client side**
3. **Include Authorization header in all protected requests**
4. **Handle token expiration gracefully**
5. **Validate and sanitize all input data**
6. **Use proper error logging in production**

## Example Usage

### Getting an Access Token
```bash
curl -X POST http://localhost/myproject/public/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@example.com", "password": "password123"}'
```

### Using the Token
```bash
curl -X GET http://localhost/myproject/public/api/v1/stock-alerts \
  -H "Authorization: Bearer {your-token-here}" \
  -H "Content-Type: application/json"
```

### Creating a Stock Alert
```bash
curl -X POST http://localhost/myproject/public/api/v1/stock-alerts \
  -H "Authorization: Bearer {your-token-here}" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "alert_type": "low_stock",
    "message": "Product running low on stock",
    "current_quantity": 5,
    "reorder_level": 10,
    "severity": "medium"
  }'
```
