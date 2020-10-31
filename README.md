# Stripe donation form with variable amounts.

This is a simple PHP script that shows a donation form, checks for a valid reCAPTCHA response and upon success sends the user to the Stripe checkout form.

Supported are reCAPTCHA v2 and v3. 

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

