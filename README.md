
# Sample Application

### This is a sample codebase where many elements have been simplified for demonstration purposes.


## Requirements : 

-  docker and docker-compose    

## Running the Application

1. Install dependencies:
```bash
    composer install
```
2. Start the application:
```bash
    sail up -d
```

3. Run migrations & Seed the database
```bash
    sail artisan migrate
    sail artisan db:seed
```
## Running Tests
To execute the application's tests, run:
```bash
sail artisan test
```

## API Documentation
### Common Headers
Ensure these headers are included in all API requests:
```
Accept: application/json
Content-Type: application/json
```
### API Endpoints

#### 1. **Users**

- **URL:** `http://localhost/api/users`
- **Method:** `GET`
- **Parameters:** None
- **Response Example:**
 ```json
[
    {
        "id": 1,
        "username": "Dave Hamill",
        "created_at": "2023-08-26T20:57:46.000000Z",
        "updated_at": "2023-08-26T20:57:46.000000Z"
    },
    {
        "id": 2,
        "username": "Mr. Will Jacobson V",
        "created_at": "2023-08-26T20:57:46.000000Z",
        "updated_at": "2023-08-26T20:57:46.000000Z"
    }
]
 ```

#### 2. **Products**

- **URL:** `http://localhost/api/products`
- **Method:** `GET`
- **Parameters:** None
- **Response Example:**
 ```json
[
    {
        "id": 1,
        "name": "quasi Schaden-McClure",
        "price": 8586428,
        "inventory": 1213,
        "created_at": "2023-08-26T20:57:46.000000Z",
        "updated_at": "2023-08-26T20:57:46.000000Z"
    },
    {
        "id": 2,
        "name": "quisquam Bergstrom, Crooks and Okuneva",
        "price": 4815810,
        "inventory": 4443,
        "created_at": "2023-08-26T20:57:46.000000Z",
        "updated_at": "2023-08-26T20:57:46.000000Z"
    }
]
 ```


#### 3. **Order Reserve**
- **URL:** `http://localhost/api/orders/reserve`
- **Method:** `POST`
- **Request Body Example:**
```json
{
    "ipg": "ipga",
    "user_id": 1,
    "username": "Dave Hamill",
    "items": [
        {
            "product_id": 3,
            "quantity": 10
        },
        {
            "product_id": 4,
            "quantity": 5
        }
    ]
}
```

- **Success Response:**
```json
{
    "message": "successfully reserved for confirm your purchase use bank urls: success if paid, failed if not paid",
    "success_url": "http:\/\/127.0.0.1\/api\/orders\/return_bank\/ipga\/MTE=?success=1",
    "failed_url": "http:\/\/127.0.0.1\/api\/orders\/return_bank\/ipga\/MTE=?success=0"
}
```
- **authentication Error Example:**
```json
{
	"message": "Unauthenticated."
}
```
- **Validation Error Example:**
```json
{
	"message": "The items.0.product_id field is required.",
	"errors": {
		"items.0.product_id": [
			"The items.0.product_id field is required."
		]
	}
}
```

#### 4. **Order Confirm**
- this will generated by order reserve api 
- **URL:** `http://localhost/api/orders/return_bank/{ipg_code}/{payment_code}?success={boolean}`
- **Method:** `GET`
- **Parameters:**
  - `ipg_code` (e.g., `ipga` or `ipgb`)
  - `payment_code` (based on payment ID)
  - `success` (boolean: `0` or `1`)
- **Success Response:**  
  - Status Code: `201`
  - Body: Empty
- **Error Examples:**
  - **Used Twice:** Status Code: `400`
    ```json
    {
        "message": "only one time you can call confirm page"
    }
    ```
  - **Invalid Payment Code:** Status Code: `404`
    ```json
    {
    	"message": "Not Found"
    }
    ```
- **Validation Error:** Status Code: `422`
```json
{
	"message": "The success field must be true or false.",
	"errors": {
		"success": [
			"The success field must be true or false."
		]
	}
}
```

#### 5. **Orders with Order Items**

- **URL:** `http://localhost/api/orders`
- **Method:** `GET`
- **Parameters:** None
- **Response Example:**
 ```json
[
    {
        "id": 1,
        "user_id": 1,
        "status": "performed",
        "reserved_at": "2023-08-26T21:01:47.000000Z",
        "created_at": "2023-08-26T21:01:47.000000Z",
        "updated_at": "2023-08-26T21:02:31.000000Z",
        "order_items": [
            {
                "id": 1,
                "order_id": 1,
                "product_id": 3,
                "user_id": 1,
                "quantity": 10,
                "created_at": "2023-08-26T21:01:47.000000Z",
                "updated_at": "2023-08-26T21:01:47.000000Z"
            },
            {
                "id": 2,
                "order_id": 1,
                "product_id": 4,
                "user_id": 1,
                "quantity": 3,
                "created_at": "2023-08-26T21:01:47.000000Z",
                "updated_at": "2023-08-26T21:01:47.000000Z"
            }
        ]
    },
    {
        "id": 6,
        "user_id": 1,
        "status": "reserved",
        "reserved_at": "2023-08-26T23:01:35.000000Z",
        "created_at": "2023-08-26T23:01:35.000000Z",
        "updated_at": "2023-08-26T23:01:35.000000Z",
        "order_items": [
            {
                "id": 9,
                "order_id": 6,
                "product_id": 3,
                "user_id": 1,
                "quantity": 10,
                "created_at": "2023-08-26T23:01:35.000000Z",
                "updated_at": "2023-08-26T23:01:35.000000Z"
            },
            {
                "id": 10,
                "order_id": 6,
                "product_id": 4,
                "user_id": 1,
                "quantity": 3,
                "created_at": "2023-08-26T23:01:35.000000Z",
                "updated_at": "2023-08-26T23:01:35.000000Z"
            }
        ]
    }
]
 ```

#### 6. **Payments**
- **URL:** `http://localhost/api/payments`
- **Method:** `GET`
- **Parameters:** None
- **Response Example:**
 ```json
[
    {
        "id": 1,
        "order_id": 1,
        "user_id": 1,
        "amount": 44199845,
        "gateway_type": "ipga",
        "status": "completed",
        "paid_at": "2023-08-26T21:02:31.000000Z",
        "created_at": "2023-08-26T21:01:47.000000Z",
        "updated_at": "2023-08-26T21:02:31.000000Z"
    },
    {
        "id": 2,
        "order_id": 2,
        "user_id": 1,
        "amount": 44199845,
        "gateway_type": "ipga",
        "status": "completed",
        "paid_at": "2023-08-26T21:11:38.000000Z",
        "created_at": "2023-08-26T21:05:53.000000Z",
        "updated_at": "2023-08-26T21:11:38.000000Z"
    }
]
 ```
