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

### Product Management

Products contain:

- Product name
- Unique product code
- Price per unit
- Tax percentage
- Stock on hand

### Customer Management

Customers contain:

- Customer name
- Unique email address

### Order Creation

Orders support:

- One customer
- One or more product items
- Product quantity
- Subtotal calculation
- Tax calculation
- Grand total calculation
- Automatic stock deduction

### Customer Order History

Customers can retrieve their order history using their email address.

### Low Stock Products

Products below a configurable stock threshold can be retrieved through an API.

### Queued Order Confirmation

After a successful order is created, a queued job is dispatched.

The job simulates an order confirmation email by writing order and customer details to the Laravel log.

No SMTP configuration is required.

### Stock Concurrency Protection

Order creation uses:

- Database transactions
- `lockForUpdate()`

This protects stock from being oversold when multiple requests attempt to purchase the same product.

### Automated Tests

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