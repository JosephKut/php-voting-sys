<?php

require "vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$Domain = "hall.ghprofit.com/";
$sent = false;
$errorMsg = '';

try {
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = 'ghprofit.com';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->Username = 'sa@ghprofit.com';
    $mail->Password = 'S-_K!aD_Kdv%';

    // Recipients
    if (empty($From) || empty($To)) {
        throw new Exception('Sender or recipient email address is missing.');
    }
    $mail->setFrom($mail->Username, $From);
    $mail->addAddress($To);

    // Content
    $mail->isHTML(true);
    $mail->Subject = $Subject ?? '';
    $mail->Body = $Body ?? '';

    // Optional: Enable verbose debug output
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;

    if ($mail->send()) {
        $sent = true;
    } else {
        $sent = false;
        $errorMsg = 'Unknown error: mail not sent.';
        echo "<div style='color:red;'>$errorMsg</div>";
    }
} catch (Exception $e) {
    $sent = false;
    $errorMsg = "No network or network issues !!!";//Mailer Error: {$mail->ErrorInfo} | Exception: " . $e->getMessage();
    echo "<div style='color:white; margin-top: -100px;'><h3>$errorMsg</h3></div>";
    echo "<div style='color:red;'>$e->getMessage</div>";
} catch (\Throwable $t) {
    $sent = false;
    $errorMsg = "Unexpected Error: " . $t->getMessage();
    echo "<div style='color:red;'>$errorMsg</div>";
}

// Optionally, you can log or display $errorMsg for debugging
// Example: error_log($errorMsg);