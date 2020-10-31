# Stripe Checkout donation form with variable amounts.

This is a simple PHP script that shows a donation form, checks for a valid reCAPTCHA response and upon success sends the user to Stripe Checkout.

![Screenshot](https://user-images.githubusercontent.com/29610198/97778606-984ada80-1b78-11eb-8395-2f8a12d9f710.jpg)

- Variable amount payments
- Minimum amount
- Multiple currencies
- Stripe API version 2020-08-27.
- reCAPTCHA v2 (Checkbox) and v3 (Invisible)
- Limit requests per minute

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

