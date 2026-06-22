<?php
require_once "./controllers/payment_controller.php";

$con = new Payment();
// $path = './images/programs/poster.png';
// $message = file_get_contents('./templates/programs.php');
// $message = str_replace("[name]", $_GET['name'], $message);
// $message = str_replace("[program]", 'Sample Program', $message);
// $message = str_replace("[facilitator]", 'Marivic', $message);
// $message = str_replace("[date]", date("F d, Y"), $message);
// $message = str_replace("[fromTime]",  date("g:i A"), $message);
// $message = str_replace("[toTime]", date("g:i A"), $message);
// $message = str_replace("[payment options]", formatOptions(), $message);
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

    return $opt_table;
}


echo formatOptions();
// echo $con->sendMail("Thank you for your registration!", $message, $_GET['rec'], $_GET['name'], $path);
