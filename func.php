<?php

include 'connect.php';
// session_start();
//validates link
function validate_link($token,$time,$hash,$mail,$un){
    $expectedHash = hash_hmac('sha256' , bin2hex($mail) . $time, $un);
    if ($expectedHash !== $hash){
        include 'invalid_c.php';
        return false;
    }

    $currentTime = time();
    if ($currentTime > $time + 3600){
        include 'expired.php';
        return false;
    }

    return true;
}

function get_post(){
    include 'connect.php';
    $getPost="SELECT POST FROM src_post";
    $format=$conn->query($getPost);
    $row=$format->fetch_assoc();
    foreach($format as $Pin){
        $list[]=$Pin;   
    }
    return $list;
}

function get_no_of_voters(){
    include 'connect.php';
    $getVoters="SELECT * FROM voters";
    $result=$conn->query($getVoters);
    $No_of_Voters=$result->num_rows;
    return $No_of_Voters;
}

function get_no_of_links_sent(){
    include 'connect.php';
    $getlinks="SELECT * FROM src_sent_links";
    $result=$conn->query($getlinks);
    $No_of_links=$result->num_rows;
    return $No_of_links;
}

function get_no_of_votes(){
    include 'connect.php';
    $getVotes="SELECT * FROM src_votes";
    $result=$conn->query($getVotes);
    $No_of_Votes=$result->num_rows;
    return $No_of_Votes;
}

function display_post(){
    include 'connect.php';
    $getPost="SELECT * FROM src_post";
    $result=$conn->query($getPost);
    //$n=1;
    foreach($result as $Pn){
        $get_Cand="SELECT * FROM src_candidate WHERE Post='$Pn[Post]'";
        $sult=$conn->query($get_Cand);
        $p = str_replace("_"," ",substr($Pn['Post'],4));
        if ($sult->num_rows > 0){
            echo "
            <div style='background: rgba(225, 225, 225, 0.15); width: 80%; margin:2% 10%; justify-content: left; align-items: center; border-radius:5px;'>
                <h3 style='color: #ddc918; margin-top:2%'>$p</h3>
            ";
            foreach($sult as $Pin){
            echo "
            <div style='width: 80%; margin:2% 15% 0 15%; display:flex; justify-content: left; align-items: center;'>
                <div style='margin:2%;'>
                    <img src=$Pin[Image] style='width:100px; height:100px; border-radius:5px;'>
                </div>
                <div style=''>
                    <h3>$Pin[Full_Name]</h3>
                    <h4>$Pin[Index_No]</h4>
                    <!--<td>$Pin[Reference_No]</td>-->
                    <!--<td>$Pin[Post]</td>-->
                </div>
            </div>";
            }
            echo "</div>";
        }
    }
}

function sort_candidate($VP){
    include 'connect.php';
    $getcandidates="SELECT * FROM src_candidate WHERE Post='$VP'";
    $result=$conn->query($getcandidates);
    $getPost="SELECT * FROM src_post WHERE Post='$VP'";
    $format=$conn->query($getPost);
    $row=$format->fetch_assoc();
    $n=1;
    $N=2;
    if ($result->num_rows>0){
        if ("Multi-Voting"==$row['Type']){
            foreach($result as $Pin){
                echo<<<EOT
                <div class="po">
                    <div><img src=$Pin[Image] width="100" height="100">$Pin[Full_Name]</div>
                    <input type="radio" name="choice" value=$n$Pin[Image]>
                </div>
                EOT;
                $n++;
            }
        }
        elseif ("Referendum"==$row['Type']) {
            foreach($result as $Pin){
                $fvalue=$Pin['Full_Name']."@".$n;
                $svalue=$Pin['Full_Name']."@".$N;
                echo <<<EOT
                <div class="vo">
                    <div style="display: flex; flex-direction: column;"><img src=$Pin[Image] width="100" height="100">$Pin[Full_Name]</div>
                    <div style="justify-content: center; align-items: center;">
                        <div style="justify-content: space-evenly; display: flex;">
                            <label for="choice"><h2>Yes</h2></label>
                            <input type="radio" style="margin-left:20px;" name="choice" value=$fvalue>
                        </div>
                        <div style="display: flex; margin-top:30px;">
                            <label for="choice"><h2>No</h2></label>
                            <input type="radio" style="margin-left:29px;" name="choice" value=$svalue>
                        </div>
                    </div>
                </div>
                EOT;
            }
        }
    }else{
        echo<<<EOT
            <h4>Candidates not avaiable yet!</h4>
        EOT;
    }
}

function save_C($VP){
    include 'connect.php';
    $getcandidates="SELECT * FROM src_candidate WHERE Post='$VP'";
    $result=$conn->query($getcandidates);
    if (!$result->num_rows>0){
        echo<<<EOT
        <script>
            document.getElementById('$VP').style.display="none";
        </script>
        EOT;}
    }

function sort_post(){
    include 'connect.php';
    $getPost="SELECT * FROM src_post";
    $result=$conn->query($getPost);
    foreach($result as $Pin){
        $Position = str_replace("_"," ",substr($Pin['Post'],4));
        echo <<<EOT
        <option value=$Pin[Post]>$Position</option>
        EOT;
    }
}

if (isset($_POST['save_choice'])){
    $php_list = $_POST['array'];
    $array = json_decode($php_list,true);
    foreach($array as $choice){
        $ch = ucwords($choice[0]);
        $table_check="SHOW TABLES LIKE '$ch'";
        $table=$conn->query($table_check);

        $getcandidates="SELECT * FROM src_candidate WHERE Post='$ch'";
        $can=$conn->query($getcandidates);
        $i = 0;
        if ($table->num_rows>0){
            if (strchr($choice[1],"@")){
                list($name,$ans) = explode("@",$choice[1]);
                $insertQuery="INSERT INTO $choice[0] (Candidate,Votes)
                VALUE('$name','$ans')";
                $conn->query($insertQuery);
            }else{
                while ($i < $choice[1]){
                    $canC = $can->fetch_assoc();
                    $i++;
                }
                $insertQuery="INSERT INTO $choice[0] (Candidate,Votes)
                VALUE('$canC[Full_Name]','$choice[1]')";
                $conn->query($insertQuery);
            }
        }
        else{
            $create_table="CREATE TABLE $choice[0](
                Candidate VARCHAR(50) NOT NULL,
                Votes VARCHAR(50) NOT NULL)";
            $conn->query($create_table);
            if (strchr($choice[1],"@")){
                list($name,$ans) = explode("@",$choice[1]);
                $insertQuery="INSERT INTO $choice[0] (Candidate,Votes)
                VALUE('$name','$ans')";
                $conn->query($insertQuery);
            }else{
                while ($i < $choice[1]){
                    $canC = $can->fetch_assoc();
                    $i++;
                }
                $insertQuery="INSERT INTO $choice[0] (Candidate,Votes)
                VALUE('$canC[Full_Name]','$choice[1]')";
                $conn->query($insertQuery);
            }
            }
        }
        session_start();
        echo $_SESSION['index'];
        $insertQ="INSERT INTO src_votes (Student_Email,Unique_Code)
                VALUE('$_SESSION[Student_Email]','$_SESSION[Unique_Code]')";
        $conn->query($insertQ);
        header("location: success.php");

        $sender="umat-srid";
        $To = "$_SESSION[Student_Email]";
        $From = 'umat-srid';
        $Subject = 'UMAT SRC';
        $SuccessMsg = "sent";
        $FailedMsg = "failed";

        $Body ="<p>Your vote has being cast and received successfully.</p>
                        <h3>Vote Successful</h3>";

        include 'mail.php';
        
        if($sent) {
            echo <<<EOT
            <script>
                alert("You would receive an email as a prove of a successful vote!");
            </script>
            EOT;
        }
        // else {
        //     echo "Mail failed!";
        // }
    }

    //checks if session has started or voter has voted
    function check(){
        //session_start();
        include 'connect.php';
        $check_if_voted="SELECT * FROM src_votes WHERE Student_Email='$_SESSION[Student_Email]'";
        $checked=$conn->query($check_if_voted);
        $get_s="SELECT * FROM src_session";
        $session=$conn->query($get_s);
        $ses=$session->fetch_assoc();
        if ($ses['session']=='stop'){
            include 'ses.stop.php';
            return false;
        }else{
            if ($checked->num_rows>0){
                include 'voted.php';
                return false;
            }else{
                return true;
            }
        }
    }

    function sort_result($VP){
        include 'connect.php';
        $table_check="SHOW TABLES LIKE '$VP'";
        $table=$conn->query($table_check);
        if ($table->num_rows>0){
            $getcandidates="SELECT * FROM src_candidate WHERE Post='$VP'";
            $result=$conn->query($getcandidates);
            $getPost="SELECT * FROM src_post WHERE Post='$VP'";
            $format=$conn->query($getPost);
            $row=$format->fetch_assoc();
            $n=1;
            $N=2;

            if ("Multi-Voting"==$row['Type']){
                $v= str_replace("_"," ",substr($VP,4));
                echo"<br><h2 align='center' style='border-bottom: none;'>$v</h2><br>";
                foreach($result as $Pin){
                    $getresult="SELECT * FROM $VP WHERE Candidate='$Pin[Full_Name]'";
                    $poll=$conn->query($getresult);
                    $pollC=$poll->num_rows;
                    echo<<<EOT
                    <div class="po">
                        <div class="poc">
                        <img src=$Pin[Image] width="100" height="100" style="border-radius:50%;"><h4>&nbsp$Pin[Full_Name]</h4>
                        </div>
                        <div id="poll"><h4>$pollC</h4></div>
                    </div>
                    <br>
                    EOT;
                }
            }
            elseif ("Referendum"==$row['Type']) {
                $v= str_replace("_"," ",substr($VP,4));
                echo"<br><h2 align='center' style='border-bottom: none;'>$v</h2><br>";
                foreach($result as $Pin){
                    $getresult="SELECT * FROM $VP WHERE Candidate='$Pin[Full_Name]' AND Votes='1'";
                    $poll=$conn->query($getresult);
                    $pollC=$poll->num_rows;
                    $getresult2="SELECT * FROM $VP WHERE Candidate='$Pin[Full_Name]' AND Votes='2'";
                    $poll2=$conn->query($getresult2);
                    $pollC2=$poll2->num_rows;
                    echo <<<EOT
                    <div class="po">
                        <div class="poc">
                        <img src=$Pin[Image] width="100" height="100" style="border-radius:50%;"><h4>&nbsp$Pin[Full_Name]</h4>
                        </div>
                        <div style="justify-content: space-between; align-items: center;">
                                <h4>Yes: $pollC</h4><br>
                                <h4>No: $pollC2</h4>
                        </div>
                    </div>
                    <br>
                    EOT;
                }
            }
        }else{
            $v= str_replace("_"," ",substr($VP,4));
            echo"<br><h2 align='center' style='border-bottom: none;'>$v</h2><br>
            <h5 align='center'>No results yet!!!</h5><br>";}
    }

?>




<script>
    var list = [];
    function go(post){
        const c =document.querySelector('input[name="choice"]:checked').value;
        let new_choice = [post,c];
        if (!c.includes("@")){
            document.getElementById("s"+post).src = c.slice(1);
            let new_choice = [post,c[0]];    
        }
        list.push(new_choice);
        // console.log(list);
        save(post);
    }

    function save(post){
        const php_list = JSON.stringify(list);
        document.getElementById('array').value=php_list;
        document.getElementById(post).style.display="none";
        const sav =`
            <h3>Saved !</h3>
        `;
        document.getElementById(post).insertAdjacentHTML("afterend",sav);
    }
    
</script>