<?php
include 'connect.php';

//validates link
function validate_link($token,$time,$hash,$mail,$un){
    $expectedHash = hash_hmac('sha256' ,  bin2hex($mail) . $time, $un);
    if ($expectedHash !== $hash){
        include 'invalid_C.php';
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
    $getPost="SELECT POST FROM post";
    $format=$jconn->query($getPost);
    $row=$format->fetch_assoc();
    foreach($format as $Pin){
        $list[]=$Pin;   
    }
    return $list;
}

function j_get_no_of_voters(){
    include 'connect.php';
    $getVoters="SELECT * FROM voters";
    $result=$con->query($getVoters);
    $No_of_Voters=$result->num_rows;
    return $No_of_Voters;
}

function jget_no_of_links_sent(){
    include 'connect.php';
    $getlinks="SELECT * FROM sent_links";
    $result=$jconn->query($getlinks);
    $No_of_links=$result->num_rows;
    return $No_of_links;
}

function j_get_no_of_votes(){
    include 'connect.php';
    $getVotes="SELECT * FROM votes";
    $result=$jconn->query($getVotes);
    $No_of_Votes=$result->num_rows;
    return $No_of_Votes;
}

function sort_post(){
    include 'connect.php';
    $getPost="SELECT * FROM post";
    $result=$jconn->query($getPost);
    foreach($result as $Pin){
        echo <<<EOT
        <option value=$Pin[Post]>$Pin[Post]</option>
        EOT;
    }
}

function sort_candidate($VP){
    include 'connect.php';
    $getcandidates="SELECT * FROM candidate WHERE Post='$VP'";
    $result=$jconn->query($getcandidates);
    $getPost="SELECT * FROM post WHERE Post='$VP'";
    $format=$jconn->query($getPost);
    $row=$format->fetch_assoc();
    $n=1;
    $N=2;
    if ($result->num_rows>0){
        if ("Multi-Voting"==$row['Type']){
            foreach($result as $Pin){
                echo<<<EOT
                <div class="po">
                    <div><img src=$Pin[Image] width="100" height="100">$Pin[Full_Name]</div>
                    <input type="radio" name="choice" value=$n><input">
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
                            <input type="radio" style="margin-left:20px;" name="choice" value=$fvalue></input>
                        </div>
                        <div style="display: flex; margin-top:30px;">
                            <label for="choice"><h2>No</h2></label>
                            <input type="radio" style="margin-left:29px;" name="choice" value=$svalue></input>
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
    $getcandidates="SELECT * FROM candidate WHERE Post='$VP'";
    $result=$jconn->query($getcandidates);
    if (!$result->num_rows>0){
        echo<<<EOT
        <script>
            document.getElementById('$VP').style.display="none";
        </script>
        EOT;}
    }

if (isset($_POST['jsave_choice'])){
    $php_list = $_POST['array'];
    $array = json_decode($php_list,true);
    foreach($array as $choice){
        $ch = ucwords($choice[0]);
        $table_check="SHOW TABLES LIKE '$ch'";
        $table=$jconn->query($table_check);

        $getcandidates="SELECT * FROM candidate WHERE Post='$ch'";
        $can=$jconn->query($getcandidates);
        $i = 0;
        if ($table->num_rows>0){
            if (strchr($choice[1],"@")){
                list($name,$ans) = explode("@",$choice[1]);
                $insertQuery="INSERT INTO $choice[0] (Candidate,Votes)
                VALUE('$name','$ans')";
                $jconn->query($insertQuery);
            }else{
                while ($i < $choice[1]){
                    $canC = $can->fetch_assoc();
                    $i++;
                }
                $insertQuery="INSERT INTO $choice[0] (Candidate,Votes)
                VALUE('$canC[Full_Name]','$choice[1]')";
                $jconn->query($insertQuery);
            }
        }
        else{
            $create_table="CREATE TABLE $choice[0](
                Candidate VARCHAR(50) NOT NULL,
                Votes VARCHAR(50) NOT NULL)";
            $jconn->query($create_table);
            if (strchr($choice[1],"@")){
                list($name,$ans) = explode("@",$choice[1]);
                $insertQuery="INSERT INTO $choice[0] (Candidate,Votes)
                VALUE('$name','$ans')";
                $jconn->query($insertQuery);
            }else{
                while ($i < $choice[1]){
                    $canC = $can->fetch_assoc();
                    $i++;
                }
                $insertQuery="INSERT INTO $choice[0] (Candidate,Votes)
                VALUE('$canC[Full_Name]','$choice[1]')";
                $jconn->query($insertQuery);
            }
            }
        }
        session_start();
        echo $_SESSION['index'];
        $insertQ="INSERT INTO votes (Student_Email,Unique_Code)
                VALUE('$_SESSION[Student_Email]','$_SESSION[Unique_Code]')";
        $jconn->query($insertQ);
        header("location: success.php");

        $sender="umat-srid";
        $Smail="josephkuttor730@gmail.com";
        $to = "$_SESSION[Student_Email]";
        $subject = "UMAT JCR";

        $mail->setFrom($Smail,$sender);
        $mail->addAddress($to);

        $mail->Subject = $subject;
        $mail->Body ="<p>Your vote has being cast and received successfully.</p>
                        <h3>Vote Successful</h3>";
        
        $mail->isHTML(true);

        if($mail->send()) {
            echo <<<EOT
            <script>
                alert "You would receive an email as a prove of a successful vote!";
            </script>
            EOT;
        }
    }

    //checks if session has started or voter has voted
    function check(){
        include 'connect.php';
        $check_if_voted="SELECT * FROM votes WHERE Student_Email='$_SESSION[Student_Email]'";
        $checked=$jconn->query($check_if_voted);
        $get_s="SELECT * FROM session";
        $session=$jconn->query($get_s);
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

    function display_post(){
        include 'connect.php';
        $getPost="SELECT * FROM post";
        $result=$jconn->query($getPost);
        //$n=1;
        foreach($result as $Pn){
            $get_Cand="SELECT * FROM candidate WHERE Post='$Pn[Post]'";
            $sult=$jconn->query($get_Cand);
            echo "
            <table>
                <tr>
                    <th colspan='5'>$Pn[Post]</th>
                </tr>
                <tr>
                    <th>Index No</th>
                    <th>Name</th>
                    <th>Reference No</th>
                    <th>Image</th>
                </tr>";
            foreach($sult as $Pin){
            echo "
            <tr>
                <td>$Pin[Index_No]</td>
                <td>$Pin[Full_Name]</td>
                <td>$Pin[Reference_No]</td>
                <!--<td>$Pin[Post]</td>-->
                <td><img src=$Pin[Image] style='width:100px; height:100px;'></td>
            </tr>";
        }
        echo "</table>";}
    }

    function sort_result($VP){
        include 'connect.php';
        $table_check="SHOW TABLES LIKE '$VP'";
        $table=$jconn->query($table_check);
        if ($table->num_rows>0){
            $getcandidates="SELECT * FROM candidate WHERE Post='$VP'";
            $result=$jconn->query($getcandidates);
            $getPost="SELECT * FROM post WHERE Post='$VP'";
            $format=$jconn->query($getPost);
            $row=$format->fetch_assoc();
            $n=1;
            $N=2;
            echo"<br><h2 align='center'>$VP</h2><br>";

            if ("Multi-Voting"==$row['Type']){
                foreach($result as $Pin){
                    $getresult="SELECT * FROM $VP WHERE Candidate='$Pin[Full_Name]'";
                    $poll=$jconn->query($getresult);
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
                foreach($result as $Pin){
                    $getresult="SELECT * FROM $VP WHERE Candidate='$Pin[Full_Name]' AND Votes='1'";
                    $poll=$jconn->query($getresult);
                    $pollC=$poll->num_rows;
                    $getresult2="SELECT * FROM $VP WHERE Candidate='$Pin[Full_Name]' AND Votes='2'";
                    $poll2=$jconn->query($getresult2);
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
            echo "<br><h2 align='center'>$VP</h2><br>
            <h5 align='center'>No results yet!!!</h5><br>";}
    }
?>

<script>
    var list = [];
    function go(post){
    $p=post;
    const c =document.querySelector('input[name="choice"]:checked').value;
    let new_choice = [post,c];
    list.push(new_choice);
    console.log(list);
    save(post);
    }

    function save(post){
        const php_list = JSON.stringify(list);
        document.getElementById('array').value=php_list;
        document.getElementById($p).style.display="none";
        const sav =`
            <h3>Saved !</h3>
        `;
        document.getElementById(post).insertAdjacentHTML("afterend",sav);
    }
    
</script>