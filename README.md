# Payment Management API By Laravel

A Laravel-based RESTful API for managing orders and payments with gateway support.

## Features

- **JWT**: Secure API endpoints
- **Order Management**: Create, read, update, and delete orders
- **Payment Processing**: Process payments with multiple gateway support
- **Payment Gateways**: Easy to add new payment providers using Strategy Pattern
- **RESTful Design**: Follows REST API best practices

## API Endpoints

### Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | Register new user |
| POST | `/api/auth/login` | Login user |
| POST | `/api/auth/logout` | Logout user |
| POST | `/api/auth/refresh` | Refresh JWT token |
| GET | `/api/auth/me` | Get current user |

### Orders

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/orders` | List all orders (with pagination) |
| GET | `/api/orders?status=pending` | Filter orders by status |
| POST | `/api/orders` | Create new order |
| GET | `/api/orders/{id}` | Get order details |
| PUT | `/api/orders/{id}` | Update order |
| DELETE | `/api/orders/{id}` | Delete order |

### Payments

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/payments` | List all payments |
| GET | `/api/payments?order_id=1` | Filter payments by order |
| GET | `/api/payments/{id}` | Get payment details |
| POST | `/api/payments/process` | Process a payment |
| GET | `/api/payments/gateways/list` | List available gateways |

## Usage Examples

### Register User
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Create Order
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "items": [
      {
        "product_name": "itme1",
        "quantity": 1,
        "price": 999.99
      },
      {
        "product_name": "itme2",
        "quantity": 1,
        "price": 99.99
      }
    ]
  }'
```

### Update Order Status
```bash
curl -X PUT http://localhost:8000/api/orders/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "items": [
      {
        "product_name": "itme1",
        "quantity": 1,
        "price": 999.99
      },
      {
        "product_name": "itme2",
        "quantity": 2,
        "price": 249.99
      }
    ]
  }'
```

### Process Payment
```bash
curl -X POST http://localhost:8000/api/payments/process \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "order_id": 1,
    "payment_method": "stripe"
  }'
```
