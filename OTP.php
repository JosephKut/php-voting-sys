<?php
session_start();
$From = 'UMAT-SRID';
$Subject = 'UMAT SRC';
$To = $_SESSION['Email'];

$otp = random_int(1000, 9999);

$Body = "<p>UMAT SRC voting system.</p><p>OTP to reset system: <b>$otp</b></p>";
$SuccessMsg = "<script>alert('An OTP has been sent!');</script>";
$FailedMsg = "<script>alert('OTP sent failed! Refresh the page!');</script>";

include 'mailer.php';

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
