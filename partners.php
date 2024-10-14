<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type');
require 'controllers/partners_controller.php';

$partners = new Partners();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode($partners->retrieveOnePartner($_GET['id']));
        } else {
            $result = $partners->retrievePartners();
            $json = $partners->utf8ize($result);
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
            if (isset($_POST['name']) || isset($_FILES['images'])) {
                if (isset($_FILES['images'])) {
                    $images = $_FILES['images'];
                    if ($images['error'] === UPLOAD_ERR_OK) {
                        $targetPath = "/images/partners/" . $images['name'];
                        if ($partners->updatePartner($_POST['id'], $_POST['name'], $targetPath)) {
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
                    if ($partners->updatePartner($_POST['id'], $_POST['name'])) {
                        echo "Learning partner updated!";
                    } else {
                        echo "Error database!";
                    }
                }
            }
        } else {
            if (isset($_POST['name']) && isset($_FILES['images'])) {
                if (isset($_FILES['images'])) {
                    $images = $_FILES['images'];
                    if ($images['error'] === UPLOAD_ERR_OK) {
                        $targetPath = "/images/partners/" . $images['name'];
                        if ($partners->insertPartner($_POST['name'], $targetPath)) {
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
        if ($partners->deletePartner($_GET["id"])) {
            echo "Name and logo deleted!";
        }
        break;
}
