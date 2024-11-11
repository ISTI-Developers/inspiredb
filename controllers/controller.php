<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'database.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Controller
{
    public $connection;
    public $statement;
    public $isConnectionSuccess;
    public $connectionError;
    function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME;

            $this->connection = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->isConnectionSuccess = true;
            $this->setStatement("SET time_zone = 'Asia/Manila';");
            $this->statement->execute();
        } catch (PDOException $e) {
            $this->connectionError = "<script defer> console.log('" . $e->getMessage() . "')</script>";
        }
    }
    function setStatement($query)
    {
        if ($this->isConnectionSuccess) {
            $this->statement = $this->connection->prepare($query);
        } else {
            echo "SERVER DOWN! Please contact the IT Department. \n";
        }
    }

    function sendMail($subject, $message, $receipientEmail = MAIL_USERNAME, $receipientName = MAIL_NAME, $imagePath = NULL)
    {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host = 'smtp.gmail.com';                       //Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   //Enable SMTP authentication
            $mail->Username = MAIL_USERNAME;                          //SMTP username
            $mail->Password = MAIL_PASSWORD;                          //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            // $mail->SMTPDebug = 2;                                    //For checking Mailing errors
            if ($imagePath) {
                if (file_exists($imagePath)) {
                    $mail->addEmbeddedImage($imagePath, 'logo', 'image.png');
                } else {
                    return "Unable to access" . $imagePath;
                }
            }
            $mail->setFrom(MAIL_FROM, MAIL_NAME);
            $mail->addAddress($receipientEmail, $receipientName);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            if ($receipientEmail !== MAIL_USERNAME) {
                $mail->addCC(MAIL_USERNAME);
            }
            if ($mail->send()) {
                return "Message has been sent";
            } else {
                return "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
            }
        } catch (Exception $e) {
            // Log error details for debugging
            error_log("PHPMailer Error: " . $e->getMessage());
            return "Message could not be sent. Mailer Error: " . $e->getMessage();
        }
    }
    function utf8ize($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = $this->utf8ize($value);
            }
        } elseif (is_object($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed->$key = $this->utf8ize($value);
            }
        } elseif (is_string($mixed)) {
            // Convert the string to UTF-8, ignore invalid sequences
            return mb_convert_encoding($mixed, 'UTF-8', 'UTF-8');
        }
        return $mixed;
    }
}