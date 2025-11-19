# Database Diagram

## Entity Relationship Diagram

```
┌─────────────────┐
│     users       │
├─────────────────┤
│ id (PK)         │
│ name            │
│ email           │
│ password        │
│ created_at      │
│ updated_at      │
└────────┬────────┘
         │
         │ 1
         │
         │ *
         │
┌────────▼────────┐         ┌─────────────────┐
│   cart_items    │         │    products      │
├─────────────────┤         ├─────────────────┤
│ id (PK)         │         │ id (PK)          │
│ user_id (FK)    │─────────│ name             │
│ product_id (FK) │──┐      │ description      │
│ quantity        │  │      │ price            │
│ created_at      │  │      │ stock            │
│ updated_at      │  │      │ created_at       │
└─────────────────┘  │      │ updated_at       │
                     │      └────────┬─────────┘
                     │               │
                     │               │ *
                     │               │
                     │               │
┌─────────────────┐  │      ┌────────▼─────────┐
│     orders      │  │      │   order_items     │
├─────────────────┤  │      ├───────────────────┤
│ id (PK)         │  │      │ id (PK)           │
│ user_id (FK)    │──┘      │ order_id (FK)     │──┐
│ order_number    │         │ product_id (FK)   │──┘
│ address         │         │ quantity          │
│ phone           │         │ price             │
│ total           │         │ created_at        │
│ status          │         │ updated_at        │
│ created_at      │         └───────────────────┘
│ updated_at      │
└─────────────────┘
```

## Table Descriptions

### users
Stores user account information.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Primary key |
| name | string | User's full name |
| email | string | User's email (unique) |
| password | string | Hashed password |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

### products
Stores product catalog information.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Primary key |
| name | string | Product name |
| description | text | Product description (nullable) |
| price | decimal(10,2) | Product price |
| stock | integer | Available stock quantity |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Business Rule:** When `stock = 0`, the product is considered `out_of_stock`.

### cart_items
Stores items in user's shopping cart.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Primary key |
| user_id | bigint (FK) | Reference to users.id |
| product_id | bigint (FK) | Reference to products.id |
| quantity | integer | Quantity of product in cart |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Unique Constraint:** (user_id, product_id) - One cart item per product per user.

### orders
Stores order information.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Primary key |
| user_id | bigint (FK) | Reference to users.id |
| order_number | string | Unique order identifier (e.g., ORD-67890ABCDEF) |
| address | text | Delivery address |
| phone | string | Contact phone number |
| total | decimal(10,2) | Total order amount |
| status | string | Order status (default: 'pending') |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Unique Constraint:** order_number must be unique.

### order_items
Stores individual items within an order.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Primary key |
| order_id | bigint (FK) | Reference to orders.id |
| product_id | bigint (FK) | Reference to products.id |
| quantity | integer | Quantity ordered |
| price | decimal(10,2) | Price at time of order |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

## Relationships

1. **users → cart_items** (1:N)
   - One user can have many cart items
   - Cascade delete: Deleting a user deletes their cart items

2. **users → orders** (1:N)
   - One user can have many orders
   - Cascade delete: Deleting a user deletes their orders

3. **products → cart_items** (1:N)
   - One product can be in many users' carts
   - Cascade delete: Deleting a product removes it from all carts

4. **products → order_items** (1:N)
   - One product can appear in many order items
   - Cascade delete: Deleting a product removes it from order history

5. **orders → order_items** (1:N)
   - One order can have many order items
   - Cascade delete: Deleting an order deletes all its items

## Indexes

- `users.email` - Unique index for email lookup
- `orders.order_number` - Unique index for order lookup
- `cart_items(user_id, product_id)` - Unique composite index
- Foreign key indexes on all FK columns for join performance

