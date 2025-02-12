<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
require "vendor/autoload.php";

$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->SMTPAuth = true;

$mail->Host = 'smtp.gmail.com';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

$mail->Port = 587;

$mail->Username = 'josephkuttor730@gmail.com';
$mail->Password = 'yqimujkasymkfzym';

$sender='umat-srid';
$Smail='josephkuttor730@gmail.com';
$to = "$_SESSION[Email]";
$subject = 'UMAT SRC';
$ms=random_int(1000,9999);
session_start();
$_SESSION['otp']=$msg;
$msg="<p>$_SESSION[Management] voting system.</p>
                    <p>OTP to reset database '$ms'";
        
$mail->setFrom($Smail,$sender);
$mail->addAddress($to);

$mail->Subject = $subject;
$mail->Body = $msg;

if($mail->send()) {
    echo <<<EOT
        <script>
            alert "An OTP has being sent!";
        </script>
    EOT;
}else {
    echo <<<EOT
        <script>
            alert "OTP sent failed! Refresh the page!";
        </script>
    EOT;
}    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>OTP Verification</title>
</head>
<body>
    <div class="container">
        <h4>Enter OTP</h4>
        <form action="src.php" method="post">
            <div class="input-field">
                <input name="otp1" type="number">
                <input name="otp2" type="number">
                <input name="otp3" type="number">
                <input name="otp4" type="number">
            </div>
            <button name="verify">Verify OTP</button>
        </form>
    </div>
    <div id="pop-up" style="display: none;">
        Pop Up
    </div>
</body>
</html>
