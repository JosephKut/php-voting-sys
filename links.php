<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>UMAT-SRID VOTING SYSTEM</title>
</head>
<body>
    <div class="wrapper" style=" height:100vh; justify-content: center;">
        <div class="pv" style="justify-content: center;">
                <div class='pv'  style="width: 70%;">
                    <h4><a href=<?php echo $_SESSION['slink']; ?>> SRC here</a></h4>
                </div>
                <div class='pv'  style="width: 70%;">
                    <h4><a href=<?php echo $_SESSION['jlink']; ?>>JCR here</a></h4>
                </div>
                <div class='pv'  style="width: 70%;">
                    <h4><a href=<?php echo $_SESSION['dlink']; ?>>DEPT here</a></h4>
                </div>
            </div>
        </div>
    </div>
</body>
</html>