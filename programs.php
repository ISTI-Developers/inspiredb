<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type');
require 'controllers/programs_controller.php';

date_default_timezone_set("Asia/Manila");
$programs = new Programs();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $post = $programs->retrieveOneProgram($_GET['id']);
            $contents = file_get_contents($post->description);
            $post->path = $post->description;
            $post->description = json_decode($contents);
            $post->registration_cutoff_date = $post->date_reg_limit;
            $post->audience_size = $post->num_reg_limit;
            echo json_encode($post);
        } else {
            $posts = $programs->retrievePrograms();

            foreach ($posts as $post) {
                $contents = file_get_contents($post->description);
                $post->path = $post->description;
                $post->description = json_decode($contents);
                $post->registration_cutoff_date = $post->date_reg_limit;
                $post->audience_size = $post->num_reg_limit;
            }
            $json = $programs->utf8ize($posts);
            $json = json_encode($json);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo 'JSON encoding error: ' . json_last_error_msg();
                echo '<pre>';
                var_dump($posts);
                echo '</pre>';
            } else {
                echo $json;
            }
        }
        break;
    case 'POST':
        if (isset($_POST['id'])) {
            if (isset($_POST['isFeatured'])) {
                $isFeatured = filter_var($_POST['isFeatured'], FILTER_VALIDATE_BOOLEAN);
                if ($programs->updateIsFeatured($_POST['id'], $isFeatured)) {
                    echo "isFeatured status updated!";
                } else {
                    echo "Error updating isFeatured status.";
                }
            } else {
                $data = json_decode($_POST['data']);
                $title = $data->title;
                $overview = $data->overview;
                $date = $data->program_date;
                $time_start = $data->time_start;
                $time_end = $data->time_end;
                $content = $data->content;
                $filePath = $data->path;
                $facilitator = $data->facilitator;
                $category = $data->category;
                $num_reg_limit = $data->num_reg_limit;
                $date_reg_limit = $data->date_reg_limit;
                $price = $data->price;
                $venue = $data->venue;

                $content = json_encode($content);
                file_put_contents($filePath, $content);

                if (isset($_FILES['image'])) {
                    $image = $_FILES['image'];
                    if ($image['error'] === UPLOAD_ERR_OK) {
                        $targetPath = "/images/programs/" . $image['name'];
                        if ($programs->updateProgram(
                            $_POST['id'],
                            $title,
                            $targetPath,
                            $facilitator,
                            $overview,
                            $filePath,
                            $venue,
                            $category,
                            $date,
                            $time_start,
                            $time_end,
                            $num_reg_limit,
                            $date_reg_limit,
                            $price,
                            'none'
                        )) {
                            $targetPath = "." . $targetPath;
                            if (move_uploaded_file($image['tmp_name'], $targetPath)) {
                                echo "Program updated!";
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
                    if ($programs->updateProgram(
                        $_POST['id'],
                        $title,
                        $data->image,
                        $facilitator,
                        $overview,
                        $filePath,
                        $venue,
                        $category,
                        $date,
                        $time_start,
                        $time_end,
                        $num_reg_limit,
                        $date_reg_limit,
                        $price,
                        'none'
                    )) {
                        echo "Program updated!";
                    } else {
                        echo "Update error";
                    }
                }
            }
        } else {
            $data = json_decode($_POST['data']);
            $image = $_FILES['image'];
            $title = $data->title;
            $overview = $data->overview;
            $date = $data->program_date;
            $time_start = $data->time_start;
            $time_end = $data->time_end;
            $content = $data->content;
            $facilitator = $data->facilitator;
            $category = $data->category;
            $num_reg_limit = $data->num_reg_limit;
            $date_reg_limit = $data->date_reg_limit;
            $price = $data->price;
            $venue = $data->venue;

            $content = json_encode($content);

            $id = uniqid() . date("YmdHis");

            $filePath = './programs/' . $id . '.json';

            file_put_contents($filePath, $content);

            if ($image['error'] === UPLOAD_ERR_OK) {
                $targetPath = "/images/programs/" . $image['name'];

                if ($programs->insertProgram(
                    $title,
                    $targetPath,
                    $facilitator,
                    $overview,
                    $filePath,
                    $venue,
                    $category,
                    $date,
                    $time_start,
                    $time_end,
                    $num_reg_limit,
                    $date_reg_limit,
                    $price
                )) {
                    $targetPath = "." . $targetPath;
                    if (move_uploaded_file($image['tmp_name'], $targetPath)) {
                        echo "New program added!";
                    } else {
                        echo "Error file upload!";
                    }
                } else {
                    echo "Error database!";
                }
            } else {
                echo "Error file!";
            }
        }

        break;
    case 'DELETE':
        if ($programs->deleteProgram($_GET["id"])) {
            echo "Program deleted!";
        }
        break;
}
