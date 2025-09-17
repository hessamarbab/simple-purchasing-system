# Sample Application

A **simple online shop demo** built for demonstration purposes. This codebase shows a basic e-commerce workflow, including:

* User management
* Product catalog
* Order reservation and confirmation
* Payment tracking

Everything has been simplified to illustrate how an online store backend can be structured and tested.

---

## Table of Contents

1. [Requirements](#requirements)
2. [Getting Started](#getting-started)
3. [Running the Application](#running-the-application)
4. [Running Tests](#running-tests)
5. [API Documentation](#api-documentation)

   * [Common Headers](#common-headers)
   * [Endpoints](#api-endpoints)

---

## Requirements

* Docker
* Docker Compose
* PHP (with Composer)

---

## Getting Started

1. Clone the repository:

```bash
git clone <repository_url>
cd <repository_folder>
```

2. Install PHP dependencies:

```bash
composer install
```

3. Start the application:

```bash
sail up -d
```

4. Run database migrations and seeders:

```bash
sail artisan migrate
sail artisan db:seed
```

---

## Running Tests

To execute tests, run:

```bash
sail artisan test
```

---

## API Documentation

### Common Headers

Include these headers in all API requests:

```
Accept: application/json
Content-Type: application/json
```

---

### API Endpoints

#### 1. Users

* **URL:** `/api/users`
* **Method:** `GET`
* **Parameters:** None
* **Response Example:**

```json
[
  {
    "id": 1,
    "username": "Dave Hamill",
    "created_at": "2023-08-26T20:57:46.000000Z",
    "updated_at": "2023-08-26T20:57:46.000000Z"
  }
]
```

---

#### 2. Products

* **URL:** `/api/products`
* **Method:** `GET`
* **Parameters:** None
* **Response Example:**

```json
[
  {
    "id": 1,
    "name": "quasi Schaden-McClure",
    "price": 8586428,
    "inventory": 1213,
    "created_at": "2023-08-26T20:57:46.000000Z",
    "updated_at": "2023-08-26T20:57:46.000000Z"
  }
]
```

---

#### 3. Order Reserve

* **URL:** `/api/orders/reserve`
* **Method:** `POST`
* **Request Body Example:**

```json
{
  "ipg": "ipga",
  "user_id": 1,
  "username": "Dave Hamill",
  "items": [
    {"product_id": 3, "quantity": 10},
    {"product_id": 4, "quantity": 5}
  ]
}
```

* **Success Response:**

```json
{
  "message": "Successfully reserved. Confirm your purchase using bank URLs.",
  "success_url": "http://127.0.0.1/api/orders/return_bank/ipga/MTE=?success=1",
  "failed_url": "http://127.0.0.1/api/orders/return_bank/ipga/MTE=?success=0"
}
```

* **Error Examples:**

**Authentication Error:**

```json
{"message": "Unauthenticated."}
```

**Validation Error:**

```json
{
  "message": "The items.0.product_id field is required.",
  "errors": {"items.0.product_id": ["The items.0.product_id field is required."]}
}
```

---

#### 4. Order Confirm

* **URL:** `/api/orders/return_bank/{ipg_code}/{payment_code}?success={boolean}`

* **Method:** `GET`

* **Parameters:**

  * `ipg_code` (e.g., `ipga`)
  * `payment_code` (payment ID)
  * `success` (boolean: `0` or `1`)

* **Success Response:** Status `201`, Body: Empty

* **Error Examples:**

**Used Twice:** Status `400`

```json
{"message": "Only one time you can call confirm page"}
```

**Invalid Payment Code:** Status `404`

```json
{"message": "Not Found"}
```

**Validation Error:** Status `422`

```json
{
  "message": "The success field must be true or false.",
  "errors": {"success": ["The success field must be true or false."]}
}
```

---

#### 5. Orders with Order Items

* **URL:** `/api/orders`
* **Method:** `GET`
* **Parameters:** None
* **Response Example:**

```json
[
  {
    "id": 1,
    "user_id": 1,
    "status": "performed",
    "reserved_at": "2023-08-26T21:01:47.000000Z",
    "order_items": [
      {"id": 1, "order_id": 1, "product_id": 3, "quantity": 10},
      {"id": 2, "order_id": 1, "product_id": 4, "quantity": 3}
    ]
  }
]
```

---

#### 6. Payments

* **URL:** `/api/payments`
* **Method:** `GET`
* **Parameters:** None
* **Response Example:**

```json
[
  {
    "id": 1,
    "order_id": 1,
    "user_id": 1,
    "amount": 44199845,
    "gateway_type": "ipga",
    "status": "completed",
    "paid_at": "2023-08-26T21:02:31.000000Z"
  }
]
```

> Note: Some parts of this README were generated with the assistance of AI (ChatGPT).
