<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type');
require 'controllers/experts_controller.php';

$experts = new Experts();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode($experts->retrieveOneExpert($_GET['id']));
        } else {
            $result = $experts->retrieveExperts();
            $json = $experts->utf8ize($result);
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
            $data = json_decode($_POST['data']);
            $images = $data->images;
            // Initialize a new array to store file items
            $fileArray = [];
            $errors = array();
            // Extract 'file' items from $_POST
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'file') === 0) {
                    array_push($fileArray, $value);
                }
            }

            // Extract 'file' items from $_FILES
            foreach ($_FILES as $key => $file) {
                if (strpos($key, 'file') === 0) {
                    $filetype = explode(".", $file['name']);
                    $targetPath = "/images/experts/" . $data->name . "_" . substr($key, -1, 1) .  "_" . date("mdYHis") . "." . $filetype[count($filetype) - 1];
                    array_push($fileArray, $targetPath);
                }
            }

            if ($experts->updateExpert($_POST['id'], $data->name, json_encode($fileArray), $data->description, $data->biography)) {
                if (isset($_FILES)) {
                    foreach ($_FILES as $index => $file) {
                        if (move_uploaded_file($file['tmp_name'], "." . $fileArray[substr($index, -1, 1)])) {
                            array_push($errors, 1);
                        } else {
                            array_push($errors, 0);
                        }
                    }
                    if (array_search(0, $errors)) {
                        echo "Error on file uploading";
                    } else {
                        echo "Expert's content successfully updated!";
                    }
                } else {
                    $diff = array_diff($images, $fileArray);

                    if (count($diff) !== 0) {
                        foreach ($diff as $path) {
                            unlink('.' . $link);
                        }
                    } else {
                        echo "Expert's content successfully updated";
                    }
                }
            }
        } else {

            if (isset($_FILES) && isset($_POST['data'])) {
                $data = json_decode($_POST['data']);
                $fileArray = array();
                $errors = array();
                foreach ($_FILES as $index => $file) {
                    if ($file['error'] === UPLOAD_ERR_OK) {
                        $filetype = explode(".", $file['name']);
                        $targetPath = "/images/experts/" . $data->name . "_" . substr($index, -1, 1) . "_" . date("mdYHis") . "." . $filetype[count($filetype) - 1];
                        array_push($fileArray, $targetPath);
                    }
                }
                if ($experts->insertExpert($data->name, json_encode($fileArray), $data->description, $data->biography)) {
                    foreach ($_FILES as $index => $file) {
                        if (move_uploaded_file($file['tmp_name'], "." . $fileArray[substr($index, -1, 1)])) {
                            array_push($errors, 1);
                        } else {
                            array_push($errors, 0);
                        }
                    }
                    if (array_search(0, $errors)) {
                        echo "Error on file uploading";
                    } else {
                        echo "Expert's content successfully added!";
                    }
                } else {
                    echo "Error Database!";
                }
            }
        }
        break;
    case 'PUT':
        $info = json_decode(file_get_contents('php://input'));
        if ($experts->updateExpert($info->id, $info->name, $info->images, $info->description, $info->biography)) {
            echo "Expert's content updated!";
        } else {
            echo "Some content has no changes.";
        }
        break;
    case 'DELETE':
        if ($experts->deleteExpert($_GET["id"])) {
            echo "Expert's content deleted!";
        }
        break;
}
