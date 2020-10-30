<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';
header('Content-Type: application/json');

include('config.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = file_get_contents('php://input');
    $body = json_decode($input);    
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
        echo json_encode(array("error" => "invalid recaptcha"));
    }

}
function createSession() {
    global $config, $body;
    //file_put_contents("debug.log", print_r($body, true));

    if ($body->amount < 5) {
        return;
    }

    \Stripe\Stripe::setApiKey($config['stripe_secret_key']);
    try {
        $checkout_session = \Stripe\Checkout\Session::create([
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
          ]],
          'mode' => $body->mode,
          'success_url' => $config['success_url'], 
          'cancel_url' => $config['cancel_url'],
        ]);

        //file_put_contents("debug.log", print_r($checkout_session, true));

        echo json_encode(['id' => $checkout_session->id]);
    } catch(\Exception $e) {
        file_put_contents("error.log", $e);
        //file_put_contents("debug.log", print_r($e, true));
    }

}