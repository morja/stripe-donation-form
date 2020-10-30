# Stripe donation form with variable amounts and reCAPTCHA protection.

This is a simple PHP script that shows a donation form, checks for a valid reCAPTCHA response and upon success sends the user to the Stripe checkout form.

## Running the sample

1. Build the server

```
composer install
```

2. Configuration

```
cp config.php.sample config.php
```

Fill in the details. You will need Stripe and reCAPTCHA keys.

https://www.stripe.com/
https://www.google.com/recaptcha/

3. Run the server

```
php -S 127.0.0.1:4242
```

4. Go to [http://localhost:4242/index.php](http://localhost:4242/index.php)
