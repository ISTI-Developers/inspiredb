<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type');
header('Content-Type: application/json');
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");

require_once "pisopay/config/config.php";
require_once "pisopay/module/MainProcess.php";
require 'controllers/programs_controller.php';

use PHP\module\MainProcess as Checkout;
$programs = new Programs();

$input = json_decode(file_get_contents('php://input'), true);
$callbackUrl = $_POST['callback_url'] ?? 'https://www.inspireleaders.com.ph/';
$customer_name = ($_POST['first_name'] ?? 'Default') . " " . ($_POST['last_name'] ?? 'Name');
$customer_email = $_POST['email_address'] ?? 'default@example.com';
$customer_phone = $_POST['mobile_number'] ?? '0000000000';
$program_id = $_POST['program_id'] ?? null;

$merchant_trace_no = "thi" . rand(0, 99999);
$quantity = $_POST['quantity'] ?? 1;

$program_name = 'Unknown Program';
$program_price = (float) ($_POST['price'] ?? 0);

if ($program_id) {
    $program_details = $programs->retrieveProgramDetails($program_id);

    if ($program_details) {
        $program_name = $program_details['title'] ?? 'Unknown Program';
        $program_price = isset($program_details['price']) ? (float) $program_details['price'] : 0;
    }
}
$amount = 0;
$f = new Checkout();
$session_id = $f->sessionGenerate();

if ($session_id) {
    $details = [
        ["name" => $program_name, "price" => $program_price, "quantity" => $quantity],
    ];
    $amount = $program_price * $quantity;
    $customer_ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    $ip_address = $customer_ip ?: gethostbyname(gethostname());

    $arrayPostData = [
        'session_id' => $session_id,
        "branch_code" => "",
        "amount" => $amount,
        "delivery_fees" => 0,
        "transaction_type" => "TEST",
        "processor_name" => "Ameer Rojo",
        "customer_name" => $customer_name,
        "customer_email" => $customer_email,
        "customer_phone" => $customer_phone,
        "customer_address" => "2F HPL Building, 60 Sen. Gil Puyat Ave., Makati City 1200 Philippines",
        "merchant_trace_no" => $merchant_trace_no,
        "merchant_callback_url" => "#",
        "callback_url" => $callbackUrl,
        "ip_address" => $ip_address,
        "expiry_date" => (new DateTime('+1 day'))->format('Y-m-d H:i:s')
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
