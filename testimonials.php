<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type');
require 'controllers/testimonials_controller.php';

$testimonials = new Testimonials();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode($testimonials->retrieveOneTestimonial($_GET['id']));
        } else {
            $result = $testimonials->retrieveTestimonials();
            $json = $testimonials->utf8ize($result);
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
        if (isset($_POST['recommendation']) && isset($_POST['name']) && isset($_POST['company']) && isset($_POST['position'])) {
            if ($testimonials->insertTestimonial($_POST['recommendation'], $_POST['name'], $_POST['company'], $_POST['position'])) {
                if ($testimonials) {
                    echo "Testimonial added!";
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
        if ($testimonials->updateTestimonial($info->id, $info->recommendation, $info->name, $info->company, $info->position)) {
            echo "Testimonial updated!";
        } else {
            echo "Some content has no changes.";
        }
        break;
    case 'DELETE':
        if ($testimonials->deleteTestimonial($_GET["id"])) {
            echo "Testimonial deleted!";
        }
        break;
}
