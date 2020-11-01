<?php
require 'vendor/autoload.php';
header('Content-Type: application/json');

include('config.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = file_get_contents('php://input');
    $body = json_decode($input);    
}

if ($body->amount < $config['min_amount']) {
    echo json_encode(array("error" => "Amount must be at least 5"));
    exit;
}

if ($config['max_requests_minute'] > 0) {
    $last_success_file = "last_success.time";
    if (!file_exists($last_success_file)) {
        touch($last_success_file);
    }
    // allow only X a request per minute
    $last_success = file_get_contents($last_success_file);
    if (time() - intval($last_success) < 60/$config['max_requests_minute']) {
        echo json_encode(array("error" => "Too many requests, please wait a moment and try again."));
        exit;
    }
    file_put_contents($last_success_file, time());

}

if ($body->recaptcha_response) {

    // Build POST request:
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret = $config['recaptcha_secret'];
    $recaptcha_response = $body->recaptcha_response;

    // Make and decode POST request:
    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $recaptcha = json_decode($recaptcha);

    // Take action based on the score returned:
    if ($recaptcha->success) {
        // Verified - send email
        createSession();
    } else {
        // Not verified - show form error
        echo json_encode(array("error" => "Invalid recaptcha"));
    }

} else {
    echo json_encode(array("error" => "Missing recaptcha"));
}

function createSession() {
    global $config, $body;
    try {

        $session_params = [
          'payment_method_types' => ['card'],
          'line_items' => [[
            'price_data' => [
              'currency' => $body->currency,
              'unit_amount' => $body->amount,
              'product_data' => [
                'name' => $config['product_name'],
                'description' => $config['product_descriptiion']
              ],
            ],
            'quantity' => 1,
            'description' => $config['product_descriptiion'],
          ]],
          'mode' => $body->mode,
          'success_url' => $config['success_url'], 
          'cancel_url' => $config['cancel_url'],
        ];

        if ($config['subscription'] && $body->mode == 'subscription') {
            $session_params['line_items'][0]['price_data']['recurring'] = [
                'interval' => $config['subscription_interval'],
                'interval_count' => $config['subscription_interval_count'],
              ];
        }

        \Stripe\Stripe::setApiKey($config['stripe_secret_key']);
        $checkout_session = \Stripe\Checkout\Session::create($session_params);

        //file_put_contents("debug.log", print_r($checkout_session, true));

        echo json_encode(['id' => $checkout_session->id]);
    } catch(\Exception $e) {
        file_put_contents("error.log", $e);
        //file_put_contents("debug.log", print_r($e, true));
    }

}