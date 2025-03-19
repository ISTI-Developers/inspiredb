<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type');
require 'controllers/registration_controller.php';
require 'controllers/programs_controller.php';
require 'controllers/payment_controller.php';

$registration = new Registration();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode($registration->retrieveOneRegistration($_GET['id']));
        } else {
            echo json_encode($registration->retrieveRegistrations());
        }
        break;
    case 'POST':
        if (
            isset($_POST['registration_type']) && isset($_POST['first_name']) &&
            isset($_POST['last_name']) && isset($_POST['email_address']) && isset($_POST['mobile_number']) && isset($_POST['address']) &&
            isset($_POST['tin_num']) && isset($_POST['source_platform']) && isset($_POST['meal']) &&
            isset($_POST['voucher']) && isset($_POST['referred_by']) && isset($_POST['program_id']) && isset($_POST['company_name']) &&
            isset($_POST['position'])
        ) {
            $more_than_ten = isset($_FILES['more_than_ten']) ? $_FILES['more_than_ten'] : null;
            $targetPath2 = ($more_than_ten && $more_than_ten['error'] === UPLOAD_ERR_OK)
                ? "/images/excelFiles/" . $more_than_ten['name']
                : null;
            if ($registration->insertRegistration(
                $_POST['registration_type'],
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['email_address'],
                $_POST['mobile_number'],
                $_POST['address'],
                $_POST['tin_num'],
                $_POST['source_platform'],
                $_POST['meal'],
                $_POST['voucher'],
                $_POST['referred_by'],
                $_POST['program_id'],
                $_POST['company_name'],
                $_POST['position'],
                $targetPath2
            )) {
                $success = true;
                if ($more_than_ten && $more_than_ten['error'] === UPLOAD_ERR_OK) {
                    $targetPath2 = "." . $targetPath2;
                    if (!move_uploaded_file($more_than_ten['tmp_name'], $targetPath2)) {
                        echo "Error uploading participants file!";
                        $success = false;
                    }
                }
                if ($success) {
                    $prog = new Programs();
                    $program = $prog->retrieveOneProgram($_POST['program_id']);
                    $message = file_get_contents('./templates/programs.php');
                    $message = str_replace("[name]", ucwords($_POST['first_name']), $message);
                    $message = str_replace("[program]", $program->title, $message);
                    $message = str_replace("[facilitator]", $program->facilitator, $message);
                    $message = str_replace("[date]", date("F d, Y", strtotime($program->program_date)), $message);
                    $message = str_replace("[fromTime]", date("g:i A", strtotime($program->time_start)), $message);
                    $message = str_replace("[toTime]", date("g:i A", strtotime($program->time_end)), $message);
                    $message = str_replace("[venue]", $program->venue, $message);

                    $mail = $registration->sendMail("Thank you for your Registration!", $message, $_POST['email_address'], $_POST['first_name'] . " " . $_POST['last_name'], '.' . $program->image);
                    if ($mail) {
                        echo "Registration complete! Kindly proceed to checkout.";
                    } else {
                        echo "Error sending email!";
                    }
                    echo "Registration successful";
                } else {
                    echo "Error uploading file!";
                }
            } else {
                echo "Error inserting registration!";
            }
        } else {
            echo "Incomplete input!";
        }
        break;
    case 'PUT':
        $info = json_decode(file_get_contents('php://input'));
        if (
            $registration->updateRegistration(
                $info->id,
                $info->registration_type,
                $info->first_name,
                $info->last_name,
                $info->email_address,
                $info->mobile_number,
                $info->address,
                $info->tin_num,
                $info->source_platform,
                $info->meal,
                $info->voucher,
                $info->referred_by,
                $info->program_id,
                $info->company_name,
                $info->position,
                $info->more_than_ten
            )
        ) {
            echo "Registration updated!";
        } else {
            echo "Some field has no changes.";
        }
        break;
    case 'DELETE':
        if ($registration->deleteRegistration($_GET["id"])) {
            echo "Registration deleted!";
        }
        break;
}

function formatOptions()
{
    $payments = new Payment();
    $options = $payments->retrievePayments();
    $opt_table = '<table style="border: 1px solid black; border-collapse: collapse;">'; // Adding border-collapse style here
    $opt_table .= '<tr style="border: 1px solid black;"><th style="border: 1px solid black;">Payment Methods</th></tr>'; // Corrected the th and tr placement

    foreach ($options as $option) {
        $opt_table .= '<tr style="border: 1px solid black;"><td style="padding: 10px;">';
        $opt_table .= '<p>Bank Name: ' . $option->bank_name . '</p>';
        $opt_table .= '<p>Account Name: ' . $option->card_name . '</p>';
        $opt_table .= '<p>Card Number: ' . $option->acc_num . '</p>';
        $opt_table .= '<p>Contact Number: ' . $option->contact_num . '</p>';
        $opt_table .= '</td></tr>';
    }
    $opt_table .= '</table>'; // Corrected the table closing tag

    return $opt_table;
}
