<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type');
require 'controllers/beverage_controller.php';

$beverage = new Beverage();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode($beverage->getBeverageById($_GET['id']));
        } else {
            $result = $beverage->getBeverages();
            $json = $beverage->utf8ize($result);
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
        if (isset($_POST['id'])) {
            if (isset($_POST['name']) || isset($_POST['price']) || isset($_FILES['image']) ) {
                if (isset($_FILES['image'])) {
                    $images = $_FILES['image'];
                    if ($images['error'] === UPLOAD_ERR_OK) {
                        $targetPath = "/images/beverages/" . $images['name'];
                        if ($beverage->updateBeverage($_POST['id'], $_POST['name'], $_POST['price'], $targetPath)) {
                            $targetPath = "." . $targetPath;
                            if (move_uploaded_file($images['tmp_name'], $targetPath)) {
                                echo "Beverage name, price, and logo added!";
                            } else {
                                echo "Error file upload!";
                            }
                        } else {
                            echo "Error database!";
                        }
                    } else {
                        echo "Error file!";
                    }
                } else {
                    if ($beverage->updateBeverage($_POST['id'], $_POST['name'], $_POST['price'])) {
                        echo "Beverage updated!";
                    } else {
                        echo "Error database!";
                    }
                }
            }
        } else {
            if (isset($_POST['name']) && isset($_POST['price']) && isset($_FILES['image'])) {
                if (isset($_FILES['image'])) {
                    $images = $_FILES['image'];
                    if ($images['error'] === UPLOAD_ERR_OK) {
                        $targetPath = "/images/beverages/" . $images['name'];
                        if ($beverage->createBeverage($_POST['name'], $_POST['price'], $targetPath)) {
                            $targetPath = "." . $targetPath;
                            if (move_uploaded_file($images['tmp_name'], $targetPath)) {
                                echo "Name and logo added!";
                            } else {
                                echo "Error file upload!";
                            }
                        } else {
                            echo "Error database!";
                        }
                    } else {
                        echo "Error file!";
                    }
                } else {
                    echo "Contents incomplete!";
                }
            }
        }
        break;
    case 'DELETE':
        if ($beverage->deleteBeverage($_GET["id"])) {
            echo "Name and logo deleted!";
        }
        break;

    case 'OPTIONS': // Pre-flight response for CORS
        header("HTTP/1.1 200 OK");
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}
