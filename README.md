# Inventory API
***
## Requirements
PHP 8.0+

MySQL

Local server (XAMPP, WAMP, etc.)

## Setup
Clone this repository.

Create the inventory database and run the provided SQL script.

Configure the database connection in database.php.

## Endpoints
GET /products

Lists all products.

GET /products/id

Just get one product by id.

POST /products

Adds a new product. 

Example JSON:

{

    "name": "Product 1",
    
    "price": 10.5,
    
    "quantity": 100,
    
    "description": "text here"
    
}

PUT /products/{id}

Updates an existing product by id. 

Example JSON:

{

    "name": "Updated Product",
    
    "price": 12.5,
    
    "quantity": 150,
    
    "description": "text here"
    
}

DELETE /products/{id}

Deletes a product by ID.

# Notes

*The database name is inventory.
*All requests require the correct Content-Type header, set to application/json when sending data.
*The frontend part can be run in visual studio with some extension, for example: live server
