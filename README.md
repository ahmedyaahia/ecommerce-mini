# E-Commerce Mini - Laravel + Vue.js

A simplified e-commerce application with Laravel 12 backend (JWT Authentication) and Vue.js 3 frontend dashboard.

## Features

### Backend (Laravel)
- JWT-based authentication (register, login, logout, get current user)
- Product management (CRUD operations)
- Shopping cart management
- Order processing with stock validation and automatic stock reduction

### Frontend (Vue.js Dashboard)
- Authentication pages (Login, Register) with JWT token storage
- Dashboard home with statistics (Total Products, Total Orders)
- Products management page with full CRUD operations
- Orders management page with order details view

## Requirements

### Backend
- PHP >= 8.2
- Composer
- MySQL/PostgreSQL/SQLite
- Laravel 12

### Frontend
- Node.js >= 18.x
- npm or yarn
- Vue.js 3
- Vue Router 4
- Pinia (state management)

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd ecommerce-mini
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   
   Edit `.env` file and set your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ecommerce_mini
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Seed the database** (optional, but recommended for testing)
   ```bash
   php artisan db:seed
   ```
   
   This will create:
   - 4 sample users (john@example.com, jane@example.com, admin@example.com, test@example.com)
   - 15 sample products with various prices and stock levels
   - Default password for all users: `password123` (admin user: `admin123`)

7. **Generate JWT secret** (if not already done)
   ```bash
   php artisan jwt:secret
   ```

8. **Install frontend dependencies**
   ```bash
   npm install
   ```

9. **Build frontend assets** (for production)
   ```bash
   npm run build
   ```

   Or run in development mode with hot reload:
   ```bash
   npm run dev
   ```

10. **Start the development server**
    ```bash
    php artisan serve
    ```

    The application will be available at `http://localhost:8000`
    
    **Note:** For development, you can run both Laravel and Vite together:
    ```bash
    composer run dev
    ```
    This will start Laravel server, queue worker, logs, and Vite dev server concurrently.

## API Documentation

Base URL: `http://localhost:8000/api`

All protected endpoints require a JWT token in the Authorization header:
```
Authorization: Bearer {your_jwt_token}
```

### Authentication Endpoints

#### Register
- **POST** `/api/auth/register`
- **Body:**
  ```json
  {
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
  }
  ```
- **Response:**
  ```json
  {
    "success": true,
    "message": "User registered successfully",
    "data": {
      "user": {...},
      "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
      "token_type": "bearer"
    }
  }
  ```

#### Login
- **POST** `/api/auth/login`
- **Body:**
  ```json
  {
    "email": "john@example.com",
    "password": "password123"
  }
  ```
- **Response:**
  ```json
  {
    "success": true,
    "message": "Login successful",
    "data": {
      "user": {...},
      "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
      "token_type": "bearer"
    }
  }
  ```

#### Logout
- **POST** `/api/auth/logout`
- **Headers:** `Authorization: Bearer {token}`
- **Response:**
  ```json
  {
    "success": true,
    "message": "Successfully logged out"
  }
  ```

#### Get Current User
- **GET** `/api/auth/me`
- **Headers:** `Authorization: Bearer {token}`
- **Response:**
  ```json
  {
    "success": true,
    "data": {
      "user": {...}
    }
  }
  ```

### Product Endpoints

All product endpoints require authentication.

#### List Products
- **GET** `/api/products`
- **Response:**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "name": "Product Name",
        "description": "Product description",
        "price": "99.99",
        "stock": 10,
        "out_of_stock": false,
        "created_at": "...",
        "updated_at": "..."
      }
    ]
  }
  ```

#### Create Product
- **POST** `/api/products`
- **Body:**
  ```json
  {
    "name": "Product Name",
    "description": "Product description",
    "price": 99.99,
    "stock": 10
  }
  ```

#### Get Product
- **GET** `/api/products/{id}`

#### Update Product
- **PUT** `/api/products/{id}`
- **Body:** (all fields optional)
  ```json
  {
    "name": "Updated Name",
    "price": 89.99,
    "stock": 5
  }
  ```

#### Delete Product
- **DELETE** `/api/products/{id}`

**Business Rule:** If `stock = 0`, the product is considered `out_of_stock`.

### Cart Endpoints

All cart endpoints require authentication.

#### Get Cart Items
- **GET** `/api/cart`
- **Response:**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "product_id": 1,
        "product_name": "Product Name",
        "quantity": 2,
        "price": "99.99",
        "subtotal": "199.98"
      }
    ]
  }
  ```

#### Add to Cart
- **POST** `/api/cart`
- **Body:**
  ```json
  {
    "product_id": 1,
    "quantity": 2
  }
  ```

#### Update Cart Item
- **PUT** `/api/cart/{id}`
- **Body:**
  ```json
  {
    "quantity": 3
  }
  ```

#### Remove from Cart
- **DELETE** `/api/cart/{id}`

### Order Endpoints

#### Create Order
- **POST** `/api/orders`
- **Headers:** `Authorization: Bearer {token}`
- **Body:**
  ```json
  {
    "address": "123 Main St, City, Country",
    "phone": "+1234567890"
  }
  ```
- **Response:**
  ```json
  {
    "success": true,
    "message": "Order created successfully",
    "data": {
      "order_number": "ORD-67890ABCDEF",
      "total": "299.97",
      "items_summary": [
        {
          "product_id": 1,
          "product_name": "Product Name",
          "quantity": 2,
          "price": "99.99",
          "subtotal": "199.98"
        }
      ],
      "address": "123 Main St, City, Country",
      "phone": "+1234567890",
      "status": "pending",
      "created_at": "..."
    }
  }
  ```

**Order Processing Logic:**
1. Validates that cart is not empty
2. Validates stock availability for all items
3. Creates order with unique order number
4. Creates order items
5. Decreases product stock
6. Clears user's cart
7. Returns order details with summary

## Database Schema

See `DATABASE_DIAGRAM.md` for a visual representation of the database structure.

### Tables

- **users** - User accounts
- **products** - Product catalog
- **cart_items** - Shopping cart items (user_id, product_id, quantity)
- **orders** - Orders (order_number, address, phone, total, status)
- **order_items** - Order line items (order_id, product_id, quantity, price)

## Testing the API

You can use tools like Postman, cURL, or any HTTP client to test the API.

### Example: Complete Flow

1. **Register a user:**
   ```bash
   curl -X POST http://localhost:8000/api/auth/register \
     -H "Content-Type: application/json" \
     -d '{"name":"John Doe","email":"john@example.com","password":"password123"}'
   ```

2. **Login:**
   ```bash
   curl -X POST http://localhost:8000/api/auth/login \
     -H "Content-Type: application/json" \
     -d '{"email":"john@example.com","password":"password123"}'
   ```
   Save the token from the response.

3. **Create a product:**
   ```bash
   curl -X POST http://localhost:8000/api/products \
     -H "Content-Type: application/json" \
     -H "Authorization: Bearer YOUR_TOKEN" \
     -d '{"name":"Laptop","description":"Gaming Laptop","price":999.99,"stock":10}'
   ```

4. **Add to cart:**
   ```bash
   curl -X POST http://localhost:8000/api/cart \
     -H "Content-Type: application/json" \
     -H "Authorization: Bearer YOUR_TOKEN" \
     -d '{"product_id":1,"quantity":2}'
   ```

5. **Create order:**
   ```bash
   curl -X POST http://localhost:8000/api/orders \
     -H "Content-Type: application/json" \
     -H "Authorization: Bearer YOUR_TOKEN" \
     -d '{"address":"123 Main St","phone":"+1234567890"}'
   ```

## Frontend (Vue.js Dashboard)

### Frontend Structure

```
resources/js/
├── App.vue                 # Root Vue component
├── app.js                  # Main entry point
├── bootstrap.js            # Axios configuration
├── router/
│   └── index.js           # Vue Router configuration
├── stores/
│   └── auth.js            # Pinia store for authentication
├── services/
│   └── api.js             # Axios API service
├── views/
│   ├── Login.vue          # Login page
│   ├── Register.vue       # Registration page
│   ├── Dashboard.vue      # Dashboard home
│   ├── Products.vue       # Products management
│   └── Orders.vue         # Orders management
└── layouts/
    └── DashboardLayout.vue # Main dashboard layout
```

### Frontend Features

#### Authentication
- **Login Page**: Email/password authentication with JWT token storage in localStorage
- **Register Page**: User registration with validation
- **Protected Routes**: Automatic redirect to login if not authenticated
- **Token Management**: Automatic token refresh and logout on 401 errors

#### Dashboard
- **Statistics Cards**: Display total products and orders
- **Navigation**: Easy navigation between Dashboard, Products, and Orders
- **User Info**: Display logged-in user name with logout functionality

#### Products Management
- **List View**: Table displaying all products with name, description, price, stock, and status
- **Create Product**: Modal form to add new products
- **Edit Product**: Update existing product information
- **Delete Product**: Remove products with confirmation
- **Stock Status**: Visual indicator for out-of-stock products

#### Orders Management
- **Orders List**: Table showing all orders with order number, customer info, total, status, and date
- **Order Details**: Modal view with complete order information including:
  - Order details (number, status, total, date)
  - Customer information (name, phone, address)
  - Order items table with product names, quantities, prices, and subtotals

### Running Frontend in Development Mode

```bash
npm run dev
```

This will start Vite dev server with hot module replacement (HMR) at `http://localhost:5173`

### Building for Production

```bash
npm run build
```

This will compile and minify assets for production.

### Running Both Backend and Frontend

Use the composer script to run everything together:

```bash
composer run dev
```

This starts:
- Laravel server (port 8000)
- Queue worker
- Laravel Pail (logs)
- Vite dev server (port 5173)

## Technology Stack

### Backend
- Laravel 12
- JWT Authentication (tymon/jwt-auth)
- MySQL/PostgreSQL/SQLite

### Frontend
- Vue.js 3 (Composition API)
- Vue Router 4
- Pinia (State Management)
- Axios (HTTP Client)
- Tailwind CSS 4 (Styling)
- Vite (Build Tool)

## License

MIT
