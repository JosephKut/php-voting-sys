<?php
session_start();

if (isset($_GET['token']) && isset($_GET['time']) && isset($_GET['hash'])){
$token = $_GET['token'];
$time = $_GET['time'];
$hash = $_GET['hash'];
}
else{
    include 'invalid.php';
    die();
}

include("connect.php");
include("func.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="vo.css">
    <link rel="stylesheet" href="login.css">
    <title>UMAT-SRID VOTING SYSTEM</title>
</head>
<body>

    <div class="login-wrapper" id="log-flex" style="display:none;">
        <form method="post" action=<?php echo $_SERVER['REQUEST_URI']; ?>>
            <h2>Login</h2>
            <div class="input-field">
                <input type="email" name="Student_Email" required>
                <label>Student Email</label>
            </div>
            <div class="input-field">
                <input type="password" name="Unique_Code" required>
                <label>Unique Code</label>
            </div>
            <button type="submit" name="signIn">Log In</button>
        </form>
        <div class="im">
            <!-- <marquee><img src="images/m.jpeg" width="250" class="simg"></marquee> -->
            <!-- <img src="images/c7.jpeg" width="150" class="simg"> -->
        </div>
    </div>

    <div class="wrapper" id="poll" style="justify-content: center; display:none;">
        <div class="ps" id="post">
            <h2>SRC</h2>
            <h2>Select Post To Vote</h2>
            <div class="po">
                <button onclick="FPre()">
                    <div class="p"><img class="img" src="images/c4.jpeg" width="100" height="100">President</div>
                </button>
            </div>
            <div class="po">
                <button onclick="FGS()">
                    <div class="p"><img class="img" src="images/c4.jpeg" width="100" height="100">General Secretary</div>
                </button>
            </div>
            <div class="po">
                <button id="FTre">
                    <div class="p"><img class="img" src="images/c4.jpeg" width="100" height="100">Treasurer</div>
                </button>
            </div>
            <div class="po">
                <button onclick="FNUGSP()">
                    <div class="p"><img  class="img" src="images/c4.jpeg" width="100" height="100">Local NUGS President</div>
                </button>
            </div>
            <div class="po">
                <button id="NUGS_S">
                    <div class="p"><img  class="img" src="images/c4.jpeg" width="100" height="100">Local NUGS Secretary</div>
                </button>
            </div>
            <div class="po">
                <button id="NUGS_T">
                    <div class="p"><img class="img" src="images/c4.jpeg" width="100" height="100">Local NUGS Treasurer</div>
                </button>
            </div>
            <div class="po">
                <button onclick="FWCom()">
                    <div class="p"><img class="img" src="images/c4.jpeg" width="100" height="100">Womens Commissioner</div>
                </button>
            </div>
            <form action="func.php" method="post">
             <button class="submit" name="save_choice" style="width: 60px">Submit</button>
             <input type="hidden" name="array" id="array">
            </form>
        </div>
        
        <div class="pv" id="Pre">
            <div id="waterm" style="background-image: url('images/s1.jpeg');"></div>
            <h2>President</h2>
            <?php
            sort_candidate('President');
            ?>
            <div class="po" id="President">
                <button class="save-choice" id="President" onclick="go('President')">Save</button>
                <?php save_C('President') ?>
            </div>
        </div>
        <div class="pv" id="GS" style="display: none;">
            <div id="waterm" style="background-image: url('images/s1.jpeg');"></div>
            <h2>General Secretary</h2>
            <?php
            sort_candidate('General_Secretary');
            ?>
            <div class="po" id="General_Secretary">
                <button class="save-choice" id="General_Secretary" onclick="go('General_Secretary')">Save</button>
                <?php save_C('General_Secretary') ?>
            </div>
        </div>
        <div class="pv" id="Tre" style="display: none;">
            <div id="waterm" style="background-image: url('images/s1.jpeg');"></div>
            <h2>Treasurer</h2>
            <?php
            sort_candidate('Treasurer');
            ?>
            <div class="po" id="Treasurer">
                <button class="save-choice" id="Treasurer" onclick="go('Treasurer')">Save</button>
                <?php save_C('Treasurer') ?>
            </div>
        </div>
        <div class="pv" id="NUGSP" style="display: none;">
            <div id="waterm" style="background-image: url('images/s1.jpeg');"></div>
            <h2>NUGS President</h2>
            <?php
            sort_candidate('NUGS_President');
            ?>
            <div class="po" id="NUGS_President">
                <button class="save-choice" id="NUGS_President" onclick="go('NUGS_President')">Save</button>
                <?php save_C('NUGS_President') ?>
            </div>
        </div>
        <div class="pv" id="NUGSS" style="display: none;">
            <div id="waterm" style="background-image: url('images/s1.jpeg');"></div>
            <h2>NUGS Secretary</h2>
            <?php
            sort_candidate('NUGS_Secretary');
            ?>
            <div class="po" id="NUGS_Secretary">
                <button class="save-choice" id="NUGS_Secretary" onclick="go('NUGS_Secretary')">Save</button>
                <?php save_C('NUGS_Secretary') ?>
            </div>
        </div>
        <div class="pv" id="NUGST" style="display: none;">
            <div id="waterm" style="background-image: url('images/s1.jpeg');"></div>
            <h2>NUGS Secretary</h2>
            <?php
            sort_candidate('NUGS_Treasurer');
            ?>
            <div class="po" id="NUGS_Treasurer">
                <button class="save-choice" id="NUGS_Treasurer" onclick="go('NUGS_Treasurer')">Save</button>
                <?php save_C('NUGS_Treasurer') ?>
            </div>
        </div>
        <div class="pv" id="WCom" style="display: none;">
            <div id="waterm" style="background-image: url('images/s1.jpeg');"></div>
            <h2>Womens Commissioner</h2>
            <?php
            sort_candidate('Womens_Commissioner');
            ?>
            <div class="po" id="Womens_Commissioner">
                <button class="save-choice" id="Womens_Commissioner" onclick="go('Womens_Commissioner')">Save</button>
                <?php save_C('Womens_Commissioner') ?>
            </div>
        </div>
    </div>
    <script src="nav.js"></script>
    <?php
    if (isset($_POST['Student_Email']) && isset($_POST['Unique_Code'])){
        $Student_Email=$_POST['Student_Email'];
        $Unique_Code=$_POST['Unique_Code'];
        if (!validate_link($token,$time,$hash,$_POST['Student_Email'],$_POST['Unique_Code'])){
            die();
        }else{
            $_SESSION['Student_Email']=$Student_Email;
            $_SESSION['Unique_Code']=$Unique_Code;
            if (!check()){
                die();
            }
            echo<<<EOT
            <script>
            document.getElementById('poll').style.display="flex";
            </script>
            EOT;
         }    
     }else{
        echo<<<EOT
        <script>
        document.getElementById('log-flex').style.display="flex";
        </script>
        EOT;
     }
    ?>
</body>
</html>