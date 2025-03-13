<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type');
require 'controllers/voucher_controller.php';

$voucher = new Voucher();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $isAdmin = isset($_GET['admin']) && $_GET['admin'] === 'true';
        if (isset($_GET['id'])) {
            echo json_encode($voucher->retrieveOneVoucher($_GET['id']));
        } else {
            $result = $voucher->retrieveVouchers($isAdmin);
            $json = json_encode($voucher->utf8ize($result));

            if (json_last_error() !== JSON_ERROR_NONE) {
                echo json_encode(["error" => "JSON encoding error: " . json_last_error_msg()]);
            } else {
                echo $json;
            }
        }
        break;
    case 'POST':
        if (isset($_POST['name']) && isset($_POST['value'])) {
            if ($voucher->insertVoucher($_POST['name'], $_POST['value'])) {
                echo json_encode(["message" => "Voucher details recorded!"]);
            } else {
                echo json_encode(["error" => "Failed to insert voucher."]);
            }
        } elseif (isset($_POST['code'])) {
            $code = $_POST['code'];
            $result = $voucher->verifyVoucher($code);
            echo json_encode(["valid" => $result]);
        } else {
            echo json_encode(["error" => "Invalid POST request."]);
        }
        break;


    case 'PUT':
        $info = json_decode(file_get_contents('php://input'));
        if ($voucher->updateVoucher($info->id, $info->name, $info->value,)) {
            echo "Voucher details updated!";
        } else {
            echo "Some content has no changes.";
        }
        break;
    case 'DELETE':
        if ($voucher->deleteVoucher($_GET["id"])) {
            echo "Voucher details deleted!";
        }
        break;
    case 'OPTIONS': // Pre-flight response for CORS
        header("HTTP/1.1 200 OK");
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}
