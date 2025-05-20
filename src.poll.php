<?php
session_start();

// if (isset($_GET['token']) && isset($_GET['time']) && isset($_GET['hash'])){
// $token = $_GET['token'];
// $time = $_GET['time'];
// $hash = $_GET['hash'];
// }
// else{
//     include 'invalid.php';
//     die();
// }

include("connect.php");
include("func.php");

$posts=get_post();
echo"
    <script>
        var postButtonID = [];
        var postID = [];
        var postContentID =[];
    </script>";
foreach($posts as $post){
    $Post[] = $post['POST'];
    $DPost[] = str_replace("_"," ",$post['POST']);
    $a = "sb".$post['POST'];
    $postButtonID[] = $a;
    $b = "s".$post['POST'];
    $postID[] = $b;
    $c = "sc".$post['POST'];
    $postContentID[] = $c;
    echo"
    <script>
        postButtonID.push('$a');
        postID.push('$b');
        postContentID.push('$c');
    </script>";   
}

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

    <!-- <div class="login-wrapper" id="log-flex" style="display:none;">
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
            <marquee><img src="images/m.jpeg" width="250" class="simg"></marquee>
            <img src="images/c7.jpeg" width="150" class="simg">
        </div>
    </div> -->

    <div class="wrapper" id="poll" style="justify-content: center;"><!-- display:none;">-->
        <div class="ps" id="post">
            <h2>SRC</h2>
            <h2>Positions</h2>
            <?php
                $i = 0;
                $posts=get_post();
                foreach($posts as $post){
                    $Position = str_replace("_"," ",$post['POST']);
                    echo<<<EOT
                        <div class="po">
                            <button id=$postButtonID[$i]>
                                <div class="p"><img id=$postID[$i] class="img" src="images/c4.jpeg" width="100" height="100">$Position</div>
                            </button>
                        </div>
                    EOT;
                    $i++;
                }
            ?>
            <form action="func.php" method="post">
             <button class="submit" name="save_choice" style="width: 60px">Submit</button>
             <input type="hidden" name="array" id="array">
            </form>
        </div>
        
        <?php
            $i = 0;
            foreach($postContentID as $postContent){
                if ($i == 0){
                    $display="flex";
                }else{
                    $display="none";
                }
                echo <<<EOT
                    <div class="pv" id= $postContent style="display: $display;">
                        <div id="waterm" style="background-image: url('images/s1.jpeg');"></div>
                        <h2>$DPost[$i]</h2> 
                EOT;
                        sort_candidate($Post[$i]);
                echo <<<EOT
                        <div class="po" id=$Post[$i]>
                            <button class="save-choice" id=$Post[$i] onclick="go('$Post[$i]')">Save</button>
                EOT;
                            save_C($Post[$i]);
                echo <<<EOT
                        </div>
                    </div>
                EOT;
                $i++;
            }
        ?>
        
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