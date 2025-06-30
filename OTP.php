<?php
session_start();
// if (!isset($class)){
//     $class = "JK";
// }
echo "<script>console.log($class);</script>";
$From = 'UMAT-SRID';
$Subject = 'UMAT-'.strtoupper($class);
$To = $_SESSION['Email'];

$otp = random_int(1000, 9999);
$_SESSION['otp'] = $otp;

$Body = "<p>UMAT $class voting system.</p><p>OTP to reset system: <b>$otp</b></p>";
$SuccessMsg = "<script>alert('An OTP has been sent!');</script>";
$FailedMsg = "<script>alert('OTP sent failed! Refresh the page!');</script>";

include 'mailer.php';
include("resources.php");

// if ($sent==true) {
//     echo $SuccessMsg;
// } else {
//     echo $FailedMsg;
// }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href=<?php echo ($Domain."style.css");?>>
    <title>OTP Verification</title>
</head>
<body>
    <div class="container">
        <h4>Enter OTP</h4>
        <form action=<?php echo ($class.".php");?> method="post">
            <div class="input-field">
                <input name="otp1" type="text" maxlength="1" pattern="\d*" inputmode="numeric" required>
                <input name="otp2" type="text" maxlength="1" pattern="\d*" inputmode="numeric" required>
                <input name="otp3" type="text" maxlength="1" pattern="\d*" inputmode="numeric" required>
                <input name="otp4" type="text" maxlength="1" pattern="\d*" inputmode="numeric" required>
            </div>
            <button name="verify">Verify OTP</button>
        </form>
    </div>
    <div id="pop-up" style="display: none;">
        Pop Up
    </div>
    <script>
    // Autofocus and auto-advance
    const inputs = document.querySelectorAll('.input-field input');
    inputs[0].focus();

    inputs.forEach((input, idx) => {
        input.addEventListener('input', (e) => {
            // Only allow digits
            input.value = input.value.replace(/[^0-9]/g, '');
            if (input.value && idx < inputs.length - 1) {
                inputs[idx + 1].focus();
            }
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && idx > 0) {
                inputs[idx - 1].focus();
            }
        });
        // Paste support
        input.addEventListener('paste', (e) => {
            const data = e.clipboardData.getData('text').replace(/\D/g, '');
            if (data.length === inputs.length) {
                inputs.forEach((inp, i) => inp.value = data[i]);
                inputs[inputs.length - 1].focus();
                e.preventDefault();
            }
        });
    });

    // Optional: Prevent form submit if not all fields are filled
    document.getElementById('otp-form').addEventListener('submit', function(e) {
        let valid = true;
        inputs.forEach(inp => {
            if (!inp.value.match(/^\d$/)) valid = false;
        });
        if (!valid) {
            e.preventDefault();
            alert('Please enter the complete 4-digit OTP.');
        }
    });
    </script>
</body>
</html>
