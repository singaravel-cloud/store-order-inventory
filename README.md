# Store Order & Inventory Mini-System

A Laravel-based Store Order & Inventory Mini-System developed as a take-home assignment.

## Tech Stack

- PHP
- Laravel
- MySQL
- Eloquent ORM
- Laravel Queue
- PHPUnit
- XAMPP

## Features

### 1. Product Management

Products contain:

- Product name
- Unique product code
- Price per unit
- Tax percentage
- Stock on hand

### 2. Customer Management

Customers contain:

- Customer name
- Unique email address

### 3. Order Creation

Orders support:

- Customer details
- Multiple product items
- Product quantity
- Subtotal calculation
- Tax calculation
- Grand total calculation
- Automatic stock deduction

### 4. Customer Order History

Orders can be retrieved using the customer's email address.

### 5. Low Stock Products

Products below a configurable stock threshold can be retrieved through an API.

### 6. Queued Order Confirmation

After an order is created, a queued job is dispatched.

The job simulates an order confirmation email by writing the order details to the Laravel log.

No SMTP configuration is required.

### 7. Stock Concurrency Protection

Order creation uses:

- Database transactions
- `lockForUpdate()`

This prevents stock from being oversold when multiple requests attempt to purchase the same product.

### 8. Automated Tests

Feature tests cover:

- Successful order creation
- Insufficient stock
- Stock overselling prevention

---

## Database Structure

The application uses the following tables:

- `products`
- `customers`
- `orders`
- `order_items`

### Relationships

```text
Customer
   |
   | 1:N
   v
Orders
   |
   | 1:N
   v
Order Items
   |
   | N:1
   v
Products