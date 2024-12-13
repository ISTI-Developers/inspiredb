<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type');
require 'controllers/inquiry_controller.php';

$inquiry = new Inquiry();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode($inquiry->retrieveOneInquiry($_GET['id']));
        } else {
            $result = $inquiry->retrieveInquiry();
            $json = $inquiry->utf8ize($result);
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
        if (isset($_POST['company_name']) && isset($_POST['company_information']) && isset($_POST['name']) && isset($_POST['designation']) && isset($_POST['email']) && ($_POST['contact_number']) && isset($_POST['event_title']) && isset($_POST['event_description']) && isset($_POST['event_setup']) && isset($_POST['event_location']) && ($_POST['is_recorded']) && isset($_POST['is_streamed']) && isset($_POST['event_date']) && isset($_POST['speaker_timeslot']) && isset($_POST['audience_size']) && isset($_POST['audience_profile']) && isset($_POST['training_objective'])) {
            $fileArray = array();
            $errors = array();
            $urls = '';

            if (isset($_FILES)) {
                foreach ($_FILES as $index => $file) {
                    if ($file['error'] === UPLOAD_ERR_OK) {
                        $filetype = explode(".", $file['name']);
                        $targetPath = "/inquiries/" . date("mdYHis") . "." . $filetype[count($filetype) - 1] . "_" . substr($index, -1, 1);
                        array_push($fileArray, $targetPath);
                    }
                }
            }

            if (count($fileArray)) {
                $urls = json_encode($fileArray);
            }

            if ($inquiry->insertInquiry($_POST['company_name'], $_POST['company_information'], $_POST['name'], $_POST['designation'], $_POST['email'], $_POST['contact_number'], $_POST['event_title'], $_POST['event_description'], $_POST['event_setup'], $_POST['event_location'], $_POST['is_recorded'], $_POST['is_streamed'], $_POST['event_date'], $_POST['speaker_timeslot'], $_POST['audience_size'], $_POST['audience_profile'], $_POST['training_objective'], $urls)) {
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
                    $message1 = file_get_contents('./templates/inquiries.php');
                    $message1 = str_replace("[name]", $_POST['name'], $message1);

                    $message2 = file_get_contents('./templates/ask.php');
                    $message2 = str_replace("[company_name]", $_POST['company_name'], $message2);
                    $message2 = str_replace("[company_information]", $_POST['company_information'], $message2);
                    $message2 = str_replace("[name]", $_POST['name'], $message2);
                    $message2 = str_replace("[designation]", $_POST['designation'], $message2);
                    $message2 = str_replace("[email]", $_POST['email'], $message2);
                    $message2 = str_replace("[contact_number]", $_POST['contact_number'], $message2);
                    $message2 = str_replace("[event_title]", $_POST['event_title'], $message2);
                    $message2 = str_replace("[event_description]", $_POST['event_description'], $message2);
                    $message2 = str_replace("[event_setup]", $_POST['event_setup'], $message2);
                    $message2 = str_replace("[event_location]", $_POST['event_location'], $message2);
                    $message2 = str_replace("[is_recorded]", $_POST['is_recorded'] === "1" ? "Yes" : "No", $message2);
                    $message2 = str_replace("[is_streamed]", $_POST['is_streamed'] === "1" ? "Yes" : "No", $message2);
                    $message2 = str_replace("[event_date]", $_POST['event_date'], $message2);
                    $message2 = str_replace("[speaker_timeslot]", $_POST['speaker_timeslot'], $message2);
                    $message2 = str_replace("[audience_size]", $_POST['audience_size'], $message2);
                    $message2 = str_replace("[audience_profile]", $_POST['audience_profile'], $message2);
                    $message2 = str_replace("[training_objective]", $_POST['training_objective'], $message2);
                    
                    $mail1 = $inquiry->sendMail("Thank you for your Inquiry!", $message1, $_POST['email'], $_POST['name']);
                    $mail2 = $inquiry->sendMail("New Inquiry!", $message2, "ameerojo@gmail.com", "", NULL, json_decode($urls, true));
                    
                    if ($mail1 && $mail2) {
                        echo "Thank you for your inquiry! We'll get back to you shortly.";
                    } else {
                        echo "Error sending email. Please try again later.";
                    }
                }
            } else {
                echo "Incomplete input.";
            }
        } else {
            echo "Incomplete input. Please fill out all required fields.";
        }
        break;
    case 'PUT':
        $info = json_decode(file_get_contents('php://input'));
        if ($inquiry->updateInquiry($info->id, $info->company_name, $info->company_information, $info->name, $info->designation, $info->email, $info->contact_number, $info->event_title, $info->event_description, $info->event_setup, $info->event_location, $info->is_recorded, $info->is_streamed, $info->event_date, $info->speaker_timeslot, $info->audience_size, $info->audience_profile, $info->training_objective, $info->documents)) {
            echo "Inquiry updated!";
        } else {
            echo "Some content has no changes.";
        }
        break;
    case 'DELETE':
        if ($inquiry->deleteInquiry($_GET["id"])) {
            echo "Inquiry deleted!";
        }
        break;
}
