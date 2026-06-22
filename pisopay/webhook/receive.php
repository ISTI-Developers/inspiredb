<?php

/*
 * Sample DATA
 * This is a sample payload only you can't use this using wrong api credentials.
 * Json Format:
    {
        "responseCode": 0,
        "responseMessage": "Successful",
        "data": {
            "status": "0",
            "statusMessage": "SUCESSFULLY PAID",
            "settlementStatus": "",
            "referenceCount": "1",
            "referenceNo": "PPGPPD890172367",
            "transactionStatusAll": "0",
            "amount": "660",
            "traceNo": "PCHBv2df4811ee840",
            "paymentChannelCode": "PPC",
            "customerName": "John Doe",
            "customerEmail": "developer@pisopay.com.ph",
            "customerPhone": "09084876064",
            "transactionId": "1115970371855984",
            "transactionDate": "2020-08-10 13:26:59",
            "postedDate": "2020-08-10 13:26:59",
            "timestamp": "1597130952",
            "hd": "356b7494d8f99430792866fba3888eb71beaf4b65377f31b1f93bb42125aecdc"
        }
    }
*/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uname = "apiilc2024"; //input api_username
    $pass = "Q2oYJ2hgffGnGQLwgulmlF78CpbWCr"; //input api_password

    $combined = $uname . ":" . $pass;

    $postBody = file_get_contents("php://input");
    $details = json_decode($postBody);

    $t = $details->data->timestamp;
    $getTrace = $details->data->traceNo;
    $allAmount = $details->data->amount;

    $auth = hash("sha256", $combined . $t);
    $auth = substr($auth, 0, 10);
    $hd1 = hash_hmac("sha256", $auth . $getTrace . $allAmount, $t);
    $hd2 = $details->data->hd;

    if (hash_equals($hd1, $hd2)) { // EXTREMELY IMPORTANT !!
        if ($hd1 === $hd2) { // EXTREMELY IMPORTANT !!
            // Check if Existing on your DB

            //THE GATEWAY NEEDS RESPONSE "OK" IF SUCCESSFUL
            echo "OK";
        } else {
            var_dump("FAILED VALIDATION2"); //HASH DATA NOT MATCHED
        }
    } else {
        var_dump("FAILED VALIDATION1"); //HASH DATA NOT MATCHED
    }
} else {
    http_response_code('405'); //method not allowed
    exit();
}
