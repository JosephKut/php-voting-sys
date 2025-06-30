<?php

require "vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
$Domain = "hall.ghprofit.com/";

$mail->isSMTP();
$mail->SMTPAuth = true;
$mail->Host = 'ghprofit.com';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
$mail->Username = 'sa@ghprofit.com';
$mail->Password = 'S-_K!aD_Kdv%';

$mail->setFrom($mail->Username, $From);
$mail->addAddress($To);

$mail->isHTML(true);
$mail->Subject = $Subject;
$mail->Body = $Body;

//$mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable debugging

if ($mail->send()) {
    $sent = true;
    echo $SuccessMsg;
} else {
    $sent =false;
    echo $FailedMsg;
}
?>