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
include("func.php");
include("resources.php");
include 'connect.php';
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
        <!-- <img src="https://acses-srid.vercel.app/"> -->
        <div id="hb">
            <a href="ad.src.php?Login=success">SRC</a>
            <a href="ad.jcr.php?Login=success">JCR</a>
            <!-- <a href="ad.dept.php?Login=success">DEPARTMENTAL</a> -->
        </div>
    </div>
    <div class="wrapper">
        <div class="but">
            <h1>SRC</h1>
            <button onclick="Fdash()">Dashboard</button>
            <button onclick="FpostM()">Post Management</button>
            <button onclick="FcandidateM()">Candidate Management</button>
            <!-- <button onclick="FdeptM()">Departmental Management</button> -->
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
                <form action="src.php" method="post">
                    <input type="radio" name="voters" value=<?php echo get_no_of_voters(); ?> checked hidden>
                    <input type="radio" name="voted" value=<?php echo get_no_of_votes(); ?> checked hidden>
                    <button name="Report" title="View Report">Report</button>
                    <button name="Reset" title="Reset Database">Reset</button>
                    <!-- <button name="Backup" title="Backup Files">Backup</button> -->
                </form>
            </div>
            <div class="dash">
                <div class="Cdash">
                    Total Voters
                    <h2><?php echo get_no_of_voters(); ?></h2>
                    <button class="views" onclick="voters()">view</button>
                </div>
                <div class="Cdash">
                    Total Links Sent
                    <h2><?php echo get_no_of_links_sent(); ?></h2>
                    <button class="views" onclick="not_sent()" >view</button>
                </div>
            </div>
            <div class="dash">
                <div class="Cdash">
                    Total Votes
                    <h2> <?php echo get_no_of_votes(); ?> </h2>
                    <button class="views" onclick="voted_info()" >view</button>
                </div>
                <div class="Sdash">
                    <form action="src.php" method="post" style="gap: 0;">
                    <input type="number" placeholder="Duration in hours" name="duration" title="4hours">
                    <div class="details">
                        <details>
                        <summary>Session</summary>
                        <button class="ses-but" name="sst" id="sst" style="width:30%; color: white; border: none; padding: 0 15px;">Start</button>
                        <button class="ses-but" name="ssp" id="ssp" style="width:30%; color: white; border: none; padding: 0 15px;">Stop</button>
                        </details>
                        <?php
                            $get_s="SELECT * FROM src_session";
                            $session=$conn->query($get_s);
                            $ses=$session->fetch_assoc();
                            if ($ses['status']== 1){
                                echo "<p style='color: green;'>Session is running</p>
                                <script>
                                const sst=document.getElementById('sst');
                                sst.style.backgroundColor='#ddc918';
                                </script>";
                            }else {
                                echo "<p style='color: red;'>Session has stopped</p>
                                <script>
                                const ssp=document.getElementById('ssp');
                                ssp.style.background='#ddc918';
                                </script>";
                            }
                        ?>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="content" id="voters" style="display: none;">
            <?php include 'voters.php';?>
        </div>

        <?php $dom = 'src';
         include 'tables.php';?>
        
        <div class="content" id="postM" style="display: none;">
            <form action="src.php" method="post">
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
                    <button  name="addP">Add</button>
                    <button name="editP">Edit</button>
                    <button name="deletP">Delete</button>
                </div>
            </form>
        </div>
        <div class="content" id="candidateM" style="display: none;">
            <form action="src.php" method="post" enctype="multipart/form-data">
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
                    <button type="submit" name="addC">Add</button>
                    <button name="editC" >Edit</button>
                    <button name="deletC">Delete</button>
                </div>
            </form>
        </div>

        <div class="content" id="viewC" style="display: none;">
            <table class="voters-table">
                <thead>
                    <tr>
                        <th colspan="3">Positions</th>
                    </tr>
                    <tr>
                        <th>Post Id</th>
                        <th>Post</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $getPost="SELECT * FROM src_post";
                        $result=$conn->query($getPost);
                        foreach($result as $Pin){
                        $Position = str_replace("_"," ",substr($Pin['Post'],4));
                        echo "
                        <tr style='color: white;'>
                            <td>$Pin[Post_id]</td>
                            <td>$Position</td>
                            <td>$Pin[Type]</td>
                        </tr>";}
                    ?>
                </tbody>
            </table>
             <?php
                display_post();
             ?>
         </div>
         <div class="content" id="viewR" style="display: none;">
            <h2>VOTING RESULTS</h2>
            <form action="src.php" method="get">
                <div class="state">
                    <button name='src_chart' title="Graphical representation of results.">Charts</button>
                </div>
            </form>
            <form action="src.php" method="post">
                <div class="state">
                    <button name='release_result' id="release" >Release Result</button>
                </div>
            </form>
            <?php
                $get_s="SELECT * FROM src_session";
                $session=$conn->query($get_s);
                $ses=$session->fetch_assoc();
                if ($ses['release']== 1){
                    echo <<<EOT
                        <script>
                            document.getElementById('release').textContent='Withold Result';
                        </script>
                    EOT;
                }else {
                    echo <<<EOT
                        <div class="results-grid">
                            <div class="results-list">
                                <h3>Results Not Released</h3>
                                <h3>Could be</h3>
                                <div class="candidate-result">
                                    <h4>there are no results</h4>
                                </div>
                                <div class="candidate-result">
                                    <h4>voting has not yet ended</h4>
                                </div>
                            </div>
                        </div>
                    EOT;
                }
                if ($ses['status']== 1) {
                    echo <<<EOT
                        <script>
                            document.getElementById('release').disabled = true;
                        </script>
                    EOT;
                }
                if ($ses['release']== 1) {
                    foreach($posts as $post){
                        sort_result($post['POST']);
                    }
                }
            ?>
         </div>

         <div class="content" id="ST" style="display: none; padding: 1%;  align-content: center;">
                <form id="state" action="src.php" method="post" enctype="multipart/form-data" style="height:100%;">
                    <!-- <h2>Notice Board</h2> -->
                    <input name="title" placeholder="Title" style="height: 50%; width: 100%; padding: 1%; margin: 1%;"></input>
                    <textarea name="statement" placeholder="Statement" style="height: 600%; width: 100%; padding: 1%; margin: 1%;"></textarea>
                    
                    <div class="button">
                        <button name="submitST">Submit</button>
                        <button name="deleteST">Delete</button>
                    </div>
                    <details>
                        <summary><h3>Preview Statement</h3></summary>
                        <?php
                        $select="SELECT * FROM src_ec_statement";
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
         </div>
    <script src=<?php echo ($Domain."nav.js"); ?> type="text/javascript"></script>
    <script src=<?php echo ($Domain."canvasjs.min.js"); ?>></script>
</body>
</html>