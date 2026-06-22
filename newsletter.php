<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type');
require 'controllers/newsletter_controller.php';

$newsletter = new Newsletter();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode($newsletter->retrieveOneNewsletter($_GET['id']));
        } else {
            // echo json_encode($newsletter->retrieveNewsletter());
            $result = $newsletter->retrieveNewsletter();
            $json = $newsletter->utf8ize($result);
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
        if (isset($_POST['name']) && isset($_POST['email'])) {
            $result = $newsletter->retrieveOneNewsletter($_POST['email']);

            if ($result) {
                echo "You have already subscribed with our newsletter!";
            } else {

                if ($newsletter->insertNewsletter($_POST['name'], $_POST['email'])) {
                    $message = file_get_contents('./templates/newsletter.php');
                    $message = str_replace("[name]", $_POST['name'], $message);
                    $mail = $newsletter->sendMail("Thank you for your Subscription!",$message, $_POST['email'], $_POST['name']);
                    if ($mail) {
                        echo "Subscribed!";
                    } else {
                        echo $mail;
                    }
                } else {
                    echo "Fill in the blanks";
                }
            }
        }
        break;
    case 'DELETE':
        if ($newsletter->deleteNewsletter($_GET["id"])) {
            echo "Subscriber information deleted!";
        }
        break;
}
