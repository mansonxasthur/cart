# Cart

This is a demo for a given task.

### Installation

- With **Docker**:
    - Run `docker-compose up -d --build`. This will run the project under [http://localhost](http://localhost).

- Locally with php installed:
    - Go inside source folder and run `composer install`.
    - Run `php -S http://localhost:80 public`. This will run the project under [http://localhost](http://localhost).

---

### Endpoints

- `POST` `/cart`
    - Required data:
        - `products` type <span style="color: cyan">*array*</span>
            - Has `products` each product with type <span style="color: cyan">*array*</span>
                - Product fields:
                    - `name` type <span style="color: magenta">*string*</span>
                    - `quantity` type <span style="color: navy">*int*</span>
        - `currency` type <span style="color: magenta">*string*</span>
            - Valid currencies <span style="color: lightgrey; background: grey; padding: 2px">*USD,EGP*</span>
    - Example:
        ```json
        {
            "products": [
                {
                    "name": "T-shirt",
                    "quantity": 1
                }
            ],
            "currency": "USD"
        }
        ```
    - Response:
        ```json
        {
            "Subtotal": "$10.99",
            "Taxes": "$1.54",
            "Discounts": "", #if there are discounts
            "Total": "$12.53"
        }
        ```
- `GET` `/products`
    - Response:
        ```json
        {
            "data":  [
                {
                    "name": "T-shirt",
                    "price": 10.99,
                    "discount": 10,
                    "discount_type": "percentage"
                }    
            ],
            "offers": []
      }
        ```
      
---
### Notes
- Wrote some test as much as I can for the given time.
- Pre assumed that products have unlimited quantity.
- You can add unlimited offers to cart by adding more offers under `src/config/offers.php`.
- You can add unlimited currencies to app by adding more currencies under `src/config/currencies.php`.
- You can add unlimited products to app by adding more products under `src/config/products.php`