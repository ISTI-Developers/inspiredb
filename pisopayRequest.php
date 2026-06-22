<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type');
header('Content-Type: application/json');
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header('Content-Type: text/html; charset=UTF-8');

require_once "pisopay/config/config.php";
require_once "pisopay/module/MainProcess.php";
require 'controllers/registration_controller.php';
require 'controllers/programs_controller.php';

use PHP\module\MainProcess as Checkout;

$registration = new Registration();
$programs = new Programs();

$input = json_decode(file_get_contents('php://input'), true);
$callbackUrl = $_POST['callback_url'] ?? 'https://www.inspireleaders.com.ph/';
$customer_name = ($_POST['first_name'] ?? 'Default') . " " . ($_POST['last_name'] ?? 'Name');
$customer_email = $_POST['email_address'] ?? 'default@example.com';
$customer_phone = $_POST['mobile_number'] ?? '0000000000';
$customer_address = $_POST['address'] ?? '2F HPL Building, 60 Sen. Gil Puyat Ave., Makati City 1200 Philippines';
$amount = $_POST['amount'] ?? 0;

$merchant_trace_no = "thi" . rand(0, 99999);

$f = new Checkout();
$details = array(
    array("name" => "In-house Training: Gravitas Leadership Payment", "price" => $amount, "quantity" => "1"),
);

$session_id = $f->sessionGenerate();

if ($session_id) {
    $customer_ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    $ip_address = $customer_ip ?: gethostbyname(gethostname());

    $arrayPostData = [
        "session_id" => $session_id,
        "branch_code" => "",
        "amount" => $amount,
        "delivery_fees" => 0,
        "transaction_type" => "Online",
        "processor_name" => "Ameer Rojo",
        "customer_name" => $customer_name,
        "customer_email" => $customer_email,
        "customer_phone" => $customer_phone,
        "customer_address" => $customer_address,
        "merchant_trace_no" => $merchant_trace_no,
        "merchant_callback_url" => "#",
        "callback_url" => $callbackUrl,
        "ip_address" => $ip_address,
        "expiry_date" => (new DateTime('+2 day'))->format('Y-m-d H:i:s')
    ];

    $token = $f->generateToken($details, $arrayPostData);

    echo json_encode([
        "session_id" => $session_id,
        "token" => $token,
        "data" => $arrayPostData,
    ], JSON_PRETTY_PRINT);
} else {
    echo json_encode(["error" => "Failed to generate session."], JSON_PRETTY_PRINT);
}
