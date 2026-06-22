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
$program_id = $_POST['program_id'] ?? null;

$customer_meal = $_POST['meal'] ?? "";
$customer_meal_price = (float) ($_POST['meal_price'] ?? 0);

$voucher = $_POST['voucher'] ?? "";
$voucher_price = (float) ($_POST['voucher_price'] ?? 0);

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

    if ($customer_meal !== "No") {
        $details[] = [
            "name" => $customer_meal,
            "price" => $customer_meal_price,
            "quantity" => 1
        ];
    }

    if (!empty($voucher)) {
        $details[] = [
            "name" => "Promo Voucher (" . number_format($voucher_price, 2) . ")",
            "price" => -$voucher_price,
            "quantity" => 1
        ];
    }

    $amount = 0;

    foreach ($details as $item) {
        $amount += $item["price"] * $item["quantity"];
    }

    // Ensure amount does not go negative
    $amount = max($amount, 0);

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

    $tokenData = json_decode($token, true);
    $checkoutLink = $tokenData['data']['url'] ?? '';

    $program = $programs->retrieveOneProgram($program_id);
    $message = file_get_contents('./templates/checkout.php');
    $message = str_replace("[name]", ucwords($_POST['first_name']), $message);
    $message = str_replace("[checkoutLink]", $checkoutLink, $message); // now using extracted link
    $message = str_replace("[program]", $program->title, $message);
    $message = str_replace("[facilitator]", $program->facilitator, $message);
    $message = str_replace("[date]", date("F d, Y", strtotime($program->program_date)), $message);
    $message = str_replace("[fromTime]", date("g:i A", strtotime($program->time_start)), $message);
    $message = str_replace("[toTime]", date("g:i A", strtotime($program->time_end)), $message);
    $message = str_replace("[venue]", $program->venue, $message);
    $message = str_replace("[price]", $program->price, $message);

    $mail = $registration->sendMail(
        "Complete Your Registration for $program->title",
        $message,
        $_POST['email_address'],
        $_POST['first_name'] . " " . $_POST['last_name'],
        '.' . $program->image
    );
} else {
    echo json_encode(["error" => "Failed to generate session."], JSON_PRETTY_PRINT);
}
