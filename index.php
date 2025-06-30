<?php
    include("resources.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href=<?php echo ($Domain."log.css");?>>
    <title>AD Login</title>
    <style>
    </style>
</head>
<body>
    <div class="login-wrapper" id="log-flex">
        <form method="post" action="ad.authentication.php">
            <h2>Login</h2>
            <div class="input-field">
                <input type="email" name="mail" required>
                <label>E-mail</label>
            </div>
            <div class="input-field">
                <input type="text" name="un" required>
                <label>Unique No</label>
            </div>
            <button type="submit" name="login">Log In</button>
        </form>
        <div class="im">
        </div>
    </div>
</body>
</html>