<?php
session_start();
$_SESSION['Email']="josephkuttor730@gmail.com";

include("resources.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href=<?php echo ($Domain."log.css");?>>
    <title>AD Registration</title>
</head>
<body>
    <div class="login-wrapper">
        <form method="post" action="ad.authentication.php" enctype="multipart/form-data">
            <h2>Admin's Registration</h2>
            <div class="input-field">
                <input type="text" name="l_name">
                <label>Last Name</label>
            </div>
            <div class="input-field">
                <input type="text" name="m_name">
                <label>Middle Name</label>
            </div>
            <div class="input-field">
                <input type="text" name="f_name">
                <label>First Name</label>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <div style="width: 45%;">
                    <div class="input-field">
                        <input type="text" name="status">
                        <label>Status</label>
                    </div>
                    <div class="input-field">
                        <input type="email" name="mail">
                        <label>Email</label>
                    </div>
                    <div class="input-field">
                        <input type="number" name="tel">
                        <label>Tel.</label>
                    </div>
                    <div class="input-field">
                        <select class="select" name="management">
                            <option value="0" selected disabled>Management</option>
                            <option value="SRC">SRC</option>
                            <option value="JCR">JCR</option>
                            <!-- <option value="ACSES">ACSES</option>
                            <option value="ELEESA">ELEESA</option>
                            <option value="MESA">MESA</option>
                            <option value="AGES">AGES</option> -->
                        </select>
                    </div>
                    <div style="margin:auto;">
                        <label for="image">Passport Image</label>
                        <input name="image" type="file" >
                    </div>
                </div>
                <div style="width: 45%; margin: 5%;">
                    <button name="submit">Submit</button>
                    <div style="display: flex; margin: 5%; justify-content: space-between;">
                        <div class="input-field">
                            <input type="text" name="select" placeholder="Unique Code">
                            <!-- <label>Unique Code</label> -->
                        </div>
                        <button name="ad_delete" style="width: 30%; height: 20%;">Delete</button>
                    </div>
                    <button>View Data</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>