<?php
    require_once "config/config.php";
    require_once "module/MainProcess.php";

use PHP\module\MainProcess as Checkout;

$f = new Checkout();

    $amount = 20;
    $delivery_fees = 0;
    $merchant_trace_no = "thi" . rand(0, 99999);
    $customer_name = "John Doe";
    $customer_email = "developer@pisopay.com.ph";
    $customer_phone = "09084876064";
    $processor_name = "Cashier01";
    $merchantCallbackUrl = "#";
    $callbackUrl = "https://devcbh.com/response";

    $details = array(
        array("name" => "itemSample", "price" => "105.50", "quantity" => "1"),
        array("name" => "itemSample1", "price" => "205.50", "quantity" => "2")
    );

    $session_id = $f->sessionGenerate();

    $arrayPostData = array(
        'session_id' => $session_id,
        "branch_code" => "",
        "amount" => $amount,
        "delivery_fees" => $delivery_fees,
        "transaction_type" => "",
        "processor_name" => $processor_name,
        "customer_name" => $customer_name,
        "customer_email" => $customer_email,
        "customer_phone" => $customer_phone,
        "customer_address" => "PH",
        "merchant_trace_no" => $merchant_trace_no,
        "merchant_callback_url" => $merchantCallbackUrl,
        "callback_url" => $callbackUrl,
        "ip_address" => "192.168.123.1",
        "expiry_date" => "2025-01-01 00:00:00" // any future date
    );

    $token = $f->generateToken($details, $arrayPostData);

    $time = time();
    $payment_channel_code = "PPC";
    $hd = $f->hashMaker($time, $merchant_trace_no, $payment_channel_code);
    $hd2 = $f->hashMaker1($hd, $merchant_trace_no, $time);

    $arrayContent = [
        "merchant_trace_no" => $merchant_trace_no,
        "ip_address" => "192.168.123.1",
        "t" => $time,
        "sms" => "ON",
        "hd" => $hd,
        "hd2" => $hd2,
        "payment_channel_code" => $payment_channel_code,
    ];

    echo $f->generateReferenceNumber($arrayContent);