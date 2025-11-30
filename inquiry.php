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

        // Required text fields
        $required = [
            'company_name',
            'company_information',
            'name',
            'designation',
            'email',
            'contact_number',
            'event_title',
            'event_description',
            'event_setup',
            'event_location',
            'event_date',
            'speaker_timeslot',
            'audience_size',
            'audience_profile',
            'training_objective'
        ];

        // Validate text fields
        foreach ($required as $key) {
            if (!isset($_POST[$key]) || trim($_POST[$key]) === "") {
                echo "Incomplete input. Missing: $key";
                exit;
            }
        }

        // Validate radios (special case because "0" is a VALID VALUE)
        if (!isset($_POST['is_recorded']) || $_POST['is_recorded'] === "") {
            echo "Incomplete input. Missing: is_recorded";
            exit;
        }

        if (!isset($_POST['is_streamed']) || $_POST['is_streamed'] === "") {
            echo "Incomplete input. Missing: is_streamed";
            exit;
        }

        // Prepare file upload paths
        $fileArray = [];
        $errors = [];

        if (!empty($_FILES)) {
            foreach ($_FILES as $index => $file) {
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $targetPath = "/inquiries/" . date("mdYHis") . "_$index.$ext";
                    $fileArray[] = $targetPath;
                }
            }
        }

        $urls = count($fileArray) ? json_encode($fileArray) : "";

        // -----------------------------
        // INSERT into database
        // -----------------------------
        $success = $inquiry->insertInquiry(
            $_POST['company_name'],
            $_POST['company_information'],
            $_POST['name'],
            $_POST['designation'],
            $_POST['email'],
            $_POST['contact_number'],
            $_POST['event_title'],
            $_POST['event_description'],
            $_POST['event_setup'],
            $_POST['event_location'],
            $_POST['is_recorded'],  // "0" or "1" OK
            $_POST['is_streamed'],  // "0" or "1" OK
            $_POST['event_date'],
            $_POST['speaker_timeslot'],
            $_POST['audience_size'],
            $_POST['audience_profile'],
            $_POST['training_objective'],
            $urls
        );

        if (!$success) {
            echo "Incomplete input.";
            exit;
        }

        // -----------------------------
        // UPLOAD FILES
        // -----------------------------
        foreach ($_FILES as $index => $file) {
            $i = array_search($targetPath, $fileArray);
            if (!move_uploaded_file($file['tmp_name'], "." . $fileArray[$i])) {
                $errors[] = 0;
            } else {
                $errors[] = 1;
            }
        }

        if (in_array(0, $errors)) {
            echo "Error on file uploading";
            exit;
        }

        // -----------------------------
        // PREPARE EMAILS
        // -----------------------------
        $message1 = file_get_contents('./templates/inquiries.php');
        $message1 = str_replace("[name]", $_POST['name'], $message1);

        $message2 = file_get_contents('./templates/ask.php');

        foreach ($_POST as $key => $value) {
            if ($key === 'is_recorded' || $key === 'is_streamed') {
                $value = $value === "1" ? "Yes" : "No";
            }
            $message2 = str_replace("[$key]", $value, $message2);
        }

        // Send emails
        $mail1 = $inquiry->sendMail(
            "Thank you for your Inquiry!",
            $message1,
            $_POST['email'],
            $_POST['name']
        );

        $mail2 = $inquiry->sendMail(
            "New Inquiry!",
            $message2,
            "ask@inspireleaders.com.ph",
            "",
            NULL,
            json_decode($urls, true)
        );

        if ($mail1 && $mail2) {
            echo "Thank you for your inquiry! We'll get back to you shortly.";
        } else {
            echo "Error sending email. Please try again later.";
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
