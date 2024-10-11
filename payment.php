<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type');
require 'controllers/payment_controller.php';

$payment = new Payment();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode($payment->retrieveOnePayment($_GET['id']));
        } else {
            $result = $payment->retrievePayments();
            $json = $payment->utf8ize($result);
            $json = json_encode($json);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo 'JSON encoding error: ' . json_last_error_msg();
                echo '<pre>';
                var_dump($result);
                echo '</pre>';
            } else {
                echo $json;
            }
        }
        break;
    case 'POST':
        if (isset($_POST['bank_name']) && isset($_POST['card_name']) && isset($_POST['acc_num']) && isset($_POST['contact_num'])) {
            if ($payment->insertPayment($_POST['bank_name'], $_POST['card_name'], $_POST['acc_num'], $_POST['contact_num'])) {
                if ($payment) {
                    echo "Payment details recorded!";
                } else {
                    echo "Incomplete details.";
                }
            } else {
                echo "Incomplete input.";
            }
        } else {
            echo "Incomplete!";
        }
        break;
    case 'PUT':
        $info = json_decode(file_get_contents('php://input'));
        // var_dump($info);
        if ($payment->updatePayment($info->id, $info->bank_name, $info->card_name, $info->acc_num, $info->contact_num)) {
            echo "Payment details updated!";
        } else {
            echo "Some content has no changes.";
        }
        break;
    case 'DELETE':
        if ($payment->deletePayment($_GET["id"])) {
            echo "Payment details deleted!";
        }
        break;
}
