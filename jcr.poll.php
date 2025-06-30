<?php
$dom = "jcr";
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
include("jfunc.php");

$posts=get_post();
echo"
    <script>
        var postButtonID = [];
        var postID = [];
        var postContentID =[];
    </script>";
foreach($posts as $post){
    $Post[] = $post['POST'];
    $DPost[] = str_replace("_"," ",substr($post['POST'],4));
    $a = "jb".$post['POST'];
    $postButtonID[] = $a;
    $b = "j".$post['POST'];
    $postID[] = $b;
    $c = "jc".$post['POST'];
    $postContentID[] = $c;
    $number_of_position = sizeof($postID);
    echo"
    <script>
        postButtonID.push('$a');
        postID.push('$b');
        postContentID.push('$c');
    </script>";   
}

    include("resources.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href=<?php echo ($Domain."vo.css");?>>
    <link rel="stylesheet" href=<?php echo ($Domain."login.css");?>>
    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> -->
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
            <!-- <marquee><img src="images/m.jpeg" width="250" class="simg"></marquee>
            <img src="images/c7.jpeg" width="150" class="simg"> -->
        </div>
    </div>

    <div class="wrapper" id="poll" style="justify-content: center; display:none;">
        <div class="ps" id="post">
            <h2>JCR</h2>
            <h2>Positions</h2>
            <?php
                $i = 0;
                $posts=get_post();
                foreach($posts as $post){
                    $Position = str_replace("_"," ",substr($post['POST'],4));
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
            <form action="jfunc.php" method="post">
                <button class="submit" name="jsave_choice" disabled style="width: 100%; padding:5px">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big w-16 h-16 text-green-500 mx-auto mb-4" aria-hidden="true">
                    <path d="m9 12 2 2 4-4"></path>
                    <path d="M5 7c0-1.1.9-2 2-2h10a2 2 0 0 1 2 2v12H5V7Z"></path>
                    <path d="M22 19H2"></path></svg>
                    <h3 width="24" height="24">Submit Ballot</h3>
                </button>
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
                        <div id="waterm" style="background-image: url('images/j1.jpeg');"></div>
                        <h2>$DPost[$i]</h2>
                    <progress class="progress" max=$number_of_position value="0" style="width:60%; accent-color: #047513;"></progress>
                EOT;
                        sort_candidate($Post[$i]);
                echo <<<EOT
                        <div class="po" id=$Post[$i]>
                            <button class="save-choice" id=$Post[$i] onclick="go('$Post[$i]')">Save</button>
                EOT;
                            save_C($Post[$i]);
                echo <<<EOT
                        </div>
                            <button class="view-summary" onclick="view_summary('flex')">View Summary</button>
                            <h6>view before submit</h6>
                    </div>
                EOT;
                $i++;
            }
        ?>

        <div class="pv" id="view-summary" style="display: none;">
            <div id="waterm" style="background-image: url('images/s1.jpeg');"></div>
            <h2>View Summary</h2>
            <div class="summary-content">
                <div id="summary">
                    <div class="summary-position">
                        <h4 class="position-name">President</h4>
                        <div class="candidates-row">
                            <img src="images/c4.jpeg" class="candidate-img" width="50" height="50" alt="Candidate"/>
                            <h5 class="candidate-name">-------------------</h5>
                        </div>
                    </div>
                </div>
                <button style = " margin:10px; " class="view-summary" onclick="view_summary('none')" ><h3>👈</h3> Back</button>
            </div>
        </div>
        
    </div>

    <script src=<?php echo ($Domain."navj.js");?>></script>
    <?php
    if (isset($_POST['Student_Email']) && isset($_POST['Unique_Code'])){
        if (!validate_link($token,$time,$hash,$_POST['Student_Email'],$_POST['Unique_Code'])){
            die();
        }else{
            $_SESSION['Student_Email']=$_POST['Student_Email'];
            $_SESSION['Unique_Code']=$_POST['Unique_Code'];
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
    <!-- <svg xmlns="http://www.w3.org/2000/svg"
        width="24" height="24" viewBox="0 0 24 24"
        fill="none" stroke="currentColor"
        stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round"
        class="lucide lucide-vote w-16 h-16 text-blue-600 mx-auto mb-4"
        aria-hidden="true">
      <path d="M12 2v20m0 0l-4-4m4 4l4-4" />
    </svg> -->
</body>
</html>