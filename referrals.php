<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type');
require 'controllers/referral_controller.php';

$referral = new Referral();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Fetch a single referral by ID
            echo json_encode($referral->retrieveOneReferral($_GET['id']));
        } elseif (isset($_GET['code'])) {
            // Verify referral code
            $code = $_GET['code'];

            $referral->setStatement("SELECT referrer_name FROM referrals WHERE referral_code = ? AND status = 1");
            $referral->statement->execute([$code]);

            $result = $referral->statement->fetch(PDO::FETCH_OBJ);

            if ($result) {
                echo json_encode(["status" => "success", "message" => "Valid referral code!", "referrer" => $result->referrer_name]);
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid referral code."]);
            }
        } else {
            // Fetch all referrals
            $result = $referral->retrieveReferrals();
            $json = $referral->utf8ize($result);
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
        if (isset($_POST['name']) && isset($_POST['code'])) {
            if ($referral->insertReferral($_POST['name'], $_POST['code'])) {
                if ($referral) {
                    echo "Referral details recorded!";
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
        if ($referral->updateReferral($info->referral_id, $info->referrer_name, $info->referral_code)) {
            echo "Referral details updated!";
        } else {
            echo "Some content has no changes.";
        }
        break;
    case 'DELETE':
        if ($referral->deleteReferral($_GET["id"])) {
            echo "Referral details deleted!";
        }
        break;
    case 'OPTIONS': // Pre-flight response for CORS
        header("HTTP/1.1 200 OK");
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}
