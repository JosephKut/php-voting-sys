<?php
if ($_GET['Login'] === "success"){
    session_start();
    if (!$_SESSION['Management']){
        header("location: index.php");
        session_destroy();
        die();    
    }
}else{
    header("location: index.php");
    die();
}
include("jfunc.php");
include("resources.php");
$posts=get_post();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href=<?php echo ($Domain."admin.css"); ?>>
    <title>UMAT-SRID VOTING SYSTEM </title>
</head>
<body>
    <div id="h">
        <h1></h1>
        <div id="hb">
            <a href="ad.src.php?Login=success">SRC</a>
            <a href="ad.jcr.php?Login=success">JCR</a>
            <!-- <a href="ad.dept.php?Login=success">DEPARTMENTAL</a> -->
        </div>
    </div>
    <div class="wrapper">
        <div class="but">
            <h1>JCR</h1>
            <button onclick="Fdash()">Dashboard</button>
            <button onclick="FpostM()">Post Management</button>
            <button onclick="FcandidateM()">Candidate Management</button>
            <button onclick="FviewC()">View Data</button>
            <button onclick="FviewR()">View Results</button>
            <!-- <button onclick="FNB()">Notice Board</button> -->
            <button onclick="FST()">Statement</button>
            <div style="color: #fff;">
                <?php
                    echo<<<EOT
                        <img src="$_SESSION[Image]" width="100" height="100" style="border-radius: 50%; justify-content: center;">
                        <h4>$_SESSION[Last_Name] $_SESSION[Middle_Name] $_SESSION[First_Name]</h4>
                        <h5>$_SESSION[Email]</h5>
                        <h5>$_SESSION[Status]</h5>
                        <h5>$_SESSION[Unique_No]</h5>
                    EOT;
                ?>
            </div>
        </div>
        <div class="content" id="dash">
            <h2>DASHBOARD</h2>
            <div class="data">
                <form action="jcr.php" method="post">
                    <input type="radio" name="voters" value=<?php echo j_get_no_of_voters(); ?> checked hidden>
                    <input type="radio" name="voted" value=<?php echo j_get_no_of_votes(); ?> checked hidden>                    
                    <button name="Report" title="View Report">Report</button>
                    <button name="Reset" title="Reset Files">Reset</button>
                    <!-- <button name="Backup" title="Backup Files">Backup</button> -->
                </form>
            </div>
            <div class="dash">
                <div class="Cdash">Total Voters <h2><?php echo j_get_no_of_voters(); ?></h2></div>
                <div class="Cdash">
                Total Links Sent
                <h2><?php echo jget_no_of_links_sent(); ?></h2>
                </div>
            </div>
            <div class="dash">
                <div class="Cdash">Total Votes<h2> <?php echo j_get_no_of_votes(); ?> </h2></div>
                <div class="Sdash">
                <form action="jcr.php" method="post">
                    <details>
                        <summary><h6>Session</h6></summary>
                        <button class="ses-but" name="jsst" id="jsst" style="width:40%; color: black;">Start</button>
                        <button class="ses-but" name="jssp" id="jssp" style="width:40%; color: black;">Stop</button>
                        <?php
                            $get_s="SELECT * FROM jcr_session";
                            $session=$conn->query($get_s);
                            $ses=$session->fetch_assoc();
                            if ($ses['session']=='start'){
                                echo "<script>
                                const jsst=document.getElementById('jsst');
                                jsst.style.backgroundColor='#ddc918';
                                </script>";
                            }else {
                                echo "<script>
                                const jssp=document.getElementById('jssp');
                                jssp.style.background='#ddc918';
                                </script>";
                            }
                        ?>
                    </details>
                </form>
                </div>
            </div>
        </div>
        <div class="content" id="postM" style="display: none;">
            <form action="jcr.php" method="post">
                <h2>Post Management</h2>
                <!-- <div class="input-field">
                    <input type="text" name="postId" required>
                    <label>Post ID</label>
                </div> -->
                <div class="input-field">
                    <input type="text" name="postName">
                    <label>Post</label>
                </div>
                <div style="margin:auto;">
                        <label for="postType">Type of Election</label>
                        <select name="postType">
                            <option value="0" selected disabled>Type</option>
                            <option value="Multi-Voting">Multi-Voting</option>
                            <option value="Referendum">Referendum</option>
                        </select>
                </div>
                <div id="ed">
                    <label>To edit or delete a candidate:</label>
                    <input class="selector" name="selector" type="text" placeholder="Enter Post Id">
                </div>
                <div>
                    <button  name="jaddP">Add</button>
                    <button name="jeditP">Edit</button>
                    <button name="jdeletP">Delete</button>
                </div>
            </form>
        </div>
        <div class="content" id="candidateM" style="display: none;">
            <form action="jcr.php" method="post" enctype="multipart/form-data">
                <h2>Candidate Management</h2>
                <div class="input-field">
                    <input type="text" name="Cname" >
                    <label>Full Name</label>
                </div>
                <div class="input-field">
                    <input type="text" name="Cindex" >
                    <label>Index No.</label>
                </div>
                <div class="input-field">
                    <input type="number" name="Creference" >
                    <label>Reference No.</label>
                </div>
                <div style="display: flex;">
                    <div style="margin:auto;">
                        <label for="postC">Position:</label>
                        <select name="postC">
                            <option value="0" selected disabled>Post</option>
                            <?php sort_post(); ?>
                        </select>
                    </div>
                    <div style="margin:auto;">
                        <label for="Image">Image of candidate</label>
                        <input name="Image" type="file" >
                    </div>
                </div>
                <div id="ed">
                    <label>To edit or delete a candidate:</label>
                    <input class="selector" type="text" name="selector" placeholder="Enter Index No. of candidate">
                </div>
                <div>
                    <button type="submit" name="jaddC">Add</button>
                    <button name="jeditC" >Edit</button>
                    <button name="jdeletC">Delete</button>
                </div>
            </form>
        </div>
        <div class="content" id="viewC" style="display: none;">
            <table>
                 <tr>
                     <th colspan="3">Positions</th>
                 </tr>
                 <tr>
                     <th>Post Id</th>
                     <th>Post</th>
                     <th>Type</th>
                 </tr>
                 <?php
                    include 'connect.php';
                    $getPost="SELECT * FROM jcr_post";
                    $result=$conn->query($getPost);
                    foreach($result as $Pin){
                        $Position = str_replace("_"," ",substr($Pin['Post'],4));
                    echo "
                    <tr>
                        <td>$Pin[Post_id]</td>
                        <td>$Position</td>
                        <td>$Pin[Type]</td>
                    </tr>";}
                 ?>
             </table>
             <?php
                display_post();
             ?>
         </div>
         <div class="content" id="viewR" style="display: none;">
            <h2>VOTING RESULTS</h2>   
            <form action="jcr.php" methiod="get">
                <div class="state">
                    <button name='jcr_chart' title="Graphical representation of results.">Charts</button>
                </div>
            </form>
            <?php
                foreach($posts as $post){
                    sort_result($post['POST']);
                }
                ?>
         </div>
         <!-- <div class="content" id="NB" style="display: none; padding: 1%; justify-content: center;  align-content: center;">
            <form action="jcr.php" method="post" enctype="multipart/form-data" style="height:100%;">
                <h2>Notice Board</h2>
                <textarea id="msg" name="msg" placeholder="Message" style="height: 600%; width: 80%; padding: 1%;"></textarea>
                <div id="ed">
                <label for="file">Include File</label>
                <input type="file" id="file" name="file">
                 </div>
                <div id="ed">
                    <label>To update or delete msg:</label>
                    <input class="selector" type="text" name="selector" placeholder="Enter SN of msg">
                </div>
                <div class="button">
                    <button name="jaddNB">Submit</button>
                    <button name="jupdateNB">Update</button>
                    <button name="jdeleteNB">Delete</button>
                </div>
            </form>
            <details>
                <summary><h3>Preview Notes</h3></summary>
                <?php
                $select="SELECT * FROM jcr_NB";
                $sel=$conn->query($select);
                foreach ($sel as $info){
                    if (!empty($info['Message'])){
                    echo<<<EOT
                        <div style="width: 80%; padding: 2%; margin: 1%;"><p title="SN.$info[SN]">$info[Message]</p></div>
                    EOT;}
                    if ($info['File']!="uploads/"){
                    echo<<<EOT
                        <div><embed src="$info[File]" title="SN.$info[SN]" style=" height:500px; width: 500px; padding:2%; border-radius:5%;"></div>
                    EOT;}
                }
              ?>
            </details>
        </div> -->

        <div class="content" id="ST" style="display: none; padding: 1%;  align-content: center;">
            <!-- <div class="state">
                <button id="Bstate">EC Stament</button>
                <button id="Bfeedback">Voters Feedback</button>
            </div> -->
                <form id="state" action="jcr.php" method="post" enctype="multipart/form-data" style="height:100%;">
                    <input name="title" placeholder="Title" style="height: 50%; width: 100%; padding: 1%; margin: 1%;"></input>
                    <textarea name="statement" placeholder="Statement" style="height: 600%; width: 100%; padding: 1%; margin: 1%;"></textarea>
                    <div class="button">
                        <button name="submitST">Submit</button>
                        <button name="deleteST">Delete</button>
                    </div>
                    <details>
                        <summary><h3>Preview Statement</h3></summary>
                        <?php
                        $select="SELECT * FROM jcr_ec_statement";
                        $sel=$conn->query($select);
                        foreach ($sel as $info){
                            if (!empty($info['Title'])){
                            echo<<<EOT
                            <div style="width: 80%; margin-left: 10%; overflow-wrap: break-word;">
                                <div style=" padding: 2%; margin: 1%;"><p>$info[Title]</p></div>
                            EOT;}
                            if (!empty($info['Statement'])){
                            echo<<<EOT
                                <div><p align="left" style="">$info[Statement]</p></div>
                            </div>
                            EOT;}
                        }
                    ?>
                    </details>
                </form>
                <!-- <div  id="feedback" style="display: none;">
                    <?php
                    $select="SELECT * FROM jcr_Feedback";
                    $sel=$conn->query($select);
                    $n=0;
                    echo"<h2>Feedbacks</h2>";
                    foreach ($sel as $info){
                        $n+=1;
                        if (!empty($info['Feedback'])){
                        echo<<<EOT
                            <div align="left" style="width: 80%; padding: 2% 2% 0 2%; margin: 0 5% 0 5%; overflow-wrap: break-word;"><p>$n. $info[Feedback]</p></div>
                        EOT;}
                    }
                    ?>
                </div> -->
        </div>
    <script src="nav.js"></script>
    <script src="canvasjs.min.js"></script>
    <!-- <script>
        const state=document.getElementById('state');
        const feedback=document.getElementById('feedback');

        document.getElementById('Bstate').addEventListener('click', function(){
            state.style.display="flex";
            feedback.style.display="none";
        })

        document.getElementById('Bfeedback').addEventListener('click', function(){
            state.style.display="none";
            feedback.style.display="block";
        })
    </script> -->
</body>
</html>