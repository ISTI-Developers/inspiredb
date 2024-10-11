<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type: *');
require 'controllers/clients_controller.php';

$clients = new Clients();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode($clients->retrieveOneClient($_GET['id']));
        } else {
            echo json_encode($clients->retrieveClients());
        }
        break;
    case 'POST':
        if (isset($_POST['id'])) {
            if (isset($_POST['brand']) || isset($_FILES['images'])) {
                if (isset($_FILES['images'])) {
                    $images = $_FILES['images'];
                    if ($images['error'] === UPLOAD_ERR_OK) {
                        $targetPath = "/images/clients/" . $images['name'];
                        if ($clients->updateClient($_POST['id'], $_POST['brand'], $targetPath)) {
                            $targetPath = "." . $targetPath;
                            if (move_uploaded_file($images['tmp_name'], $targetPath)) {
                                echo "Brand name and logo added!";
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
                    if ($clients->updateClient($_POST['id'], $_POST['brand'])) {
                        echo "Client updated!";
                    } else {
                        echo "Error database!";
                    }
                }
            }
        } else {
            if (isset($_POST['brand']) && isset($_FILES['images'])) {
                if (isset($_FILES['images'])) {
                    $images = $_FILES['images'];
                    if ($images['error'] === UPLOAD_ERR_OK) {
                        $targetPath = "/images/clients/" . $images['name'];
                        if ($clients->insertClient($_POST['brand'], $targetPath)) {
                            $targetPath = "." . $targetPath;
                            if (move_uploaded_file($images['tmp_name'], $targetPath)) {
                                echo "Brand name and logo added!";
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
        if ($clients->deleteClient($_GET["id"])) {
            echo "Brand name and logo deleted!";
        }
        break;
}
