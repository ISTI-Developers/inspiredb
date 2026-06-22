<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type');
require 'controllers/package_controller.php';

$packages = new PackageMeal();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode($packages->getPackageById($_GET['id']));
        } else {
            $result = $packages->getPackages();
            $json = $packages->utf8ize($result);
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
            if (isset($_POST['name']) || isset($_POST['meals']) || isset($_POST['price']) || isset($_POST['category']) || isset($_FILES['image'])) {
                if (isset($_FILES['image'])) {
                    $images = $_FILES['image'];
                    if ($images['error'] === UPLOAD_ERR_OK) {
                        $targetPath = "/images/packages/" . $images['name'];
                        if ($packages->updatePackage(
                            $_POST['id'],
                            $_POST['name'],
                            $_POST['meals'],
                            $_POST['price'],
                            $_POST['category'],
                            $targetPath
                        )) {
                            $targetPath = "." . $targetPath;
                            if (move_uploaded_file($images['tmp_name'], $targetPath)) {
                                echo "Package name, meals, price, category and image added!";
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
                    if ($packages->updatePackage($_POST['id'], $_POST['name'], $_POST['meals'], $_POST['price'], $_POST['category'])) {
                        echo "Packages updated!";
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
                        $targetPath = "/images/packages/" . $images['name'];
                        if ($packages->createPackage(
                            $_POST['name'],
                            $_POST['meals'],
                            $_POST['price'],
                            $_POST['category'],
                            $targetPath
                        )) {
                            $targetPath = "." . $targetPath;
                            if (move_uploaded_file($images['tmp_name'], $targetPath)) {
                                echo "Added!";
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
        if ($packages->deletePackage($_GET["id"])) {
            echo "Deleted!";
        }
        break;

    case 'OPTIONS': // Pre-flight response for CORS
        header("HTTP/1.1 200 OK");
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}
