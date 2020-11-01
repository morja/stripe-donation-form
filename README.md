# Stripe Checkout donation form with variable amounts.

This is a simple PHP script that shows a donation form, checks for a valid reCAPTCHA response and upon success sends the user to Stripe Checkout.

<img src="https://user-images.githubusercontent.com/29610198/97800138-26839700-1c33-11eb-95d5-81817390d0f5.jpg" width="350" alt="Screenshot">

- Variable amount payments
- Multiple currencies
- Subscription mode for recurring donations
- Stripe API version 2020-08-27
- Minimum amount (seems to help against scam not to allow very small amounts)
- reCAPTCHA v2 (Checkbox) and v3 (Invisible)
- Limit requests per minute (might help against scam)

## Running the app

### Build the server

```
composer install
```

### Configuration

```
cp config.php.sample config.php
```

Fill in the details. You will need Stripe and reCAPTCHA keys.

https://www.stripe.com/
https://www.google.com/recaptcha/

### Run the server

```
php -S 127.0.0.1:4242
```

### Go to 

[http://localhost:4242/index.php](http://localhost:4242/index.php)

### To-dos

Improve html / css / js on index.php. 

