### this is a samle code and too many things simplified!

# Requirements : 

-  docker and docker-compose    

# Run Application

```bash
    sail up -d
    sail artisan migrate
    sail artisan db:seed
```
### for run test :
```bash
sail artisan test
```

# Apis 
## Headers
### **use this headers for all apis**
```
Accept : application/json
Content-Type : application/json
```

## users

### Url : http://localhost/api/users
### Method : GET
### Params : no parametter needed
### Return like this :
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

## products

### Url : http://localhost/api/products
### Method : GET
### Params : no parametter needed
### Return like this :
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


### Url : http://localhost/api/orders/reserve
### Method : POST
### body like this :
```json
{
    "ipg" : "ipga",
    "user_id" : 1,
    "username" : "Dave Hamill",
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

### Return like this :
```json
{
    "message": "successfully reserved for confirm your purchase use bank urls : success if paid , failed if not paid",
    "success_url": "http:\/\/127.0.0.1\/api\/orders\/return_bank\/ipga\/MTE=?success=1",
    "failed_url": "http:\/\/127.0.0.1\/api\/orders\/return_bank\/ipga\/MTE=?success=0"
}
```
### or return this one if your username and user_id not match :
```json
{
	"message": "Unauthenticated."
}
```
### or you may get an validation error like this :
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

## order confirm
- this will generate by order reserve api 
### Url : http://localhost/api/orders/return_bank/{ipg_code}/{payment_code}?success={boolean}
### Method : GET
### parameters : 
- ipg_code : values (ipga or ipgb)
- payment_code : based on payment_id
- success : means payment done correctly or not - values (0 or 1)
### Return like this:
- status code **201**
- body : empty
### Or return like this if you use that twice:
- status code **400**
```json
{
    "message": "only one time you can call confirm page"
}
```
### Or return like this if you use wrong payment_code:
- status code **404**
```json
{
	"message": "Not Found"
}
```
### or you may get an validation error like this :
- status code **422**
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

## orders with order-items

### Url : http://localhost/api/orders
### Method : GET
### Params : no parametter needed
### Return like this :
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

## payments

### Url : http://localhost/api/payments
### Method : GET
### Params : no parametter needed
### Return like this :
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
