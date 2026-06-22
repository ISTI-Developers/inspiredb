<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type');
require 'controllers/banners_controller.php';

$banners = new Banners();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode($banners->retrieveOneBanner($_GET['id']));
        } else {
            echo json_encode($banners->retrieveBanners());
        }
        break;
    case 'POST':
        if (isset($_POST['id'])) {
            if (isset($_POST['name']) || isset($_FILES['banner'])) {
                if (isset($_FILES['banner'])) {
                    $banner = $_FILES['banner'];
                    if ($banner['error'] === UPLOAD_ERR_OK) {
                        $targetPath = "/images/banners/" . $banner['name'];
                        if ($banners->updateBanner($_POST['id'], $_POST['name'], $targetPath)) {
                            $targetPath = "." . $targetPath;
                            if (move_uploaded_file($banner['tmp_name'], $targetPath)) {
                                echo "Banner added!";
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
                    if ($banners->updateBanner($_POST['id'], $_POST['name'])) {
                        echo "Banner updated!";
                    } else {
                        echo "Error database!";
                    }
                }
            }
        } else {
            if (isset($_POST['name']) && isset($_FILES['banner'])) {
                if (isset($_FILES['banner'])) {
                    $banner = $_FILES['banner'];
                    if ($banner['error'] === UPLOAD_ERR_OK) {
                        $targetPath = "/images/banners/" . $banner['name'];
                        if ($banners->insertBanner($_POST['name'], $targetPath)) {
                            $targetPath = "." . $targetPath;
                            if (move_uploaded_file($banner['tmp_name'], $targetPath)) {
                                echo "Banner added!";
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
        if ($banners->deleteBanner($_GET["id"])) {
            echo "Banner deleted!";
        }
        break;
}
