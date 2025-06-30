<?php
include 'connect.php';

//validates link
function validate_link($token,$time,$hash,$mail,$un){
    $dom = "jcr";
    $expectedHash = hash_hmac('sha256' ,  bin2hex($mail) . $time, $un);
    if ($expectedHash !== $hash){
        echo "<h4 style='color: red;'>Invalid Credentials</h4>";
        return false;
    }

    $currentTime = time();
        $get="SELECT * FROM jcr_session";
        $session=$conn->query($get_s);
        $ses=$session->fetch_assoc();
    if ($currentTime > $time + ses['duration']){
        include 'expired.php';
        return false;
    }

    return true;
}

function get_post(){
    include 'connect.php';
    $getPost="SELECT POST FROM jcr_post";
    $format=$conn->query($getPost);
    $row=$format->fetch_assoc();
    foreach($format as $Pin){
        $list[]=$Pin;   
    }
    return $list;
}

function j_get_no_of_voters(){
    include 'connect.php';
    $getVoters="SELECT * FROM voters";
    $result=$conn->query($getVoters);
    $No_of_Voters=$result->num_rows;
    return $No_of_Voters;
}

function jget_no_of_links_sent(){
    include 'connect.php';
    $getlinks="SELECT * FROM jcr_sent_links";
    $result=$conn->query($getlinks);
    $No_of_links=$result->num_rows;
    return $No_of_links;
}

function j_get_no_of_votes(){
    include 'connect.php';
    $getVotes="SELECT * FROM jcr_votes";
    $result=$conn->query($getVotes);
    $No_of_Votes=$result->num_rows;
    return $No_of_Votes;
}

function sort_post(){
    include 'connect.php';
    $getPost="SELECT * FROM jcr_post";
    $result=$conn->query($getPost);
    foreach($result as $Pin){
        $Position = str_replace("_"," ",substr($Pin['Post'],4));
        echo <<<EOT
        <option value=$Pin[Post]>$Position</option>
        EOT;
    }
}

function sort_candidate($VP){
    include 'connect.php';
    $getcandidates="SELECT * FROM jcr_candidate WHERE Post='$VP'";
    $result=$conn->query($getcandidates);
    $getPost="SELECT * FROM jcr_post WHERE Post='$VP'";
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
                    <input type="radio" name="choice" value=$n$Pin[Image]><input">
                    <input type="hidden" name="choice_name" class="$VP" value='$Pin[Full_Name]'>
                </div>
                EOT;
                $n++;
            }
        }
        elseif ("Referendum"==$row['Type']) {
            foreach($result as $Pin){
                $fvalue=$Pin['Full_Name']."@".$n."@".$Pin['Image'];
                $svalue=$Pin['Full_Name']."@".$N."@".$Pin['Image'];
                echo <<<EOT
                <div class="vo">
                    <div style="display: flex; flex-direction: column;"><img src=$Pin[Image] width="100" height="100">$Pin[Full_Name]</div>
                    <div style="justify-content: center; align-items: center;">
                        <div style="justify-content: space-evenly; display: flex;">
                            <label for="choice"><h2>Yes</h2></label>
                            <input type="radio" style="margin-left:20px;" name="choice" value='$fvalue'></input>
                        </div>
                        <div style="display: flex; margin-top:30px;">
                            <label for="choice"><h2>No</h2></label>
                            <input type="radio" style="margin-left:29px;" name="choice" value='$svalue'></input>
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
    $getcandidates="SELECT * FROM jcr_candidate WHERE Post='$VP'";
    $result=$conn->query($getcandidates);
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
        $table=$conn->query($table_check);

        $getcandidates="SELECT * FROM jcr_candidate WHERE Post='$ch'";
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
        $insertQ="INSERT INTO jcr_votes (Student_Email,Unique_Code)
                VALUE('$_SESSION[Student_Email]','$_SESSION[Unique_Code]')";
        $conn->query($insertQ);
        header("location: success.php");

        $To = "$_SESSION[Student_Email]";
        $From = 'umat-srid';
        $Subject = 'UMAT JCR';
        $SuccessMsg = "sent";
        $FailedMsg = "failed";

        $Body ="<p>Your vote has being cast and received successfully.</p>
                        <h3>Vote Successful</h3>";
        
        include 'mailer.php';

        if($sent) {
            echo <<<EOT
            <script>
                alert("You would receive an email as a prove of a successful vote!");
            </script>
            EOT;
        }
    }

    //checks if session has started or voter has voted
    function check(){
        $dom = "jcr";
        include 'connect.php';
        $check_if_voted="SELECT * FROM jcr_votes WHERE Student_Email='$_SESSION[Student_Email]'";
        $checked=$conn->query($check_if_voted);
        $get_s="SELECT * FROM jcr_session";
        $session=$conn->query($get_s);
        $ses=$session->fetch_assoc();
        if ($ses['status']==0){
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
        $getPost="SELECT * FROM jcr_post";
        $result=$conn->query($getPost);
        //$n=1;
        foreach($result as $Pn){
            $get_Cand="SELECT * FROM jcr_candidate WHERE Post='$Pn[Post]'";
            $sult=$conn->query($get_Cand);
            $p = str_replace("_"," ",substr($Pn['Post'],4));
            if ($sult->num_rows > 0){
                echo "
                <div class='results-list' style='background: rgba(225, 225, 225, 0.15); width: 80%; margin:2% 10%; justify-content: left; align-items: center; border-radius:5px;'>
                    <h3 style='color: #ddc918; margin-top:2%'>$p</h3>
                ";
                foreach($sult as $Pin){
                echo "
                <div class='candidate-result' style='margin: 5px auto; width:80%;'>
                    <img src=$Pin[Image] style='width:100px; height:100px; border-radius:5px;'>
                    <div class='candidate-info'>
                        <div class='candidate-name'>
                            $Pin[Full_Name]
                            <h4>$Pin[Index_No]</h4>
                        </div>
                    </div>
                </div>
                ";
                }
                echo "</div>";
            }
        }
    }

    function no_of_votes(){
        include 'connect.php';
        $getVotes="SELECT * FROM jcr_votes";
        $result=$conn->query($getVotes);
        $No_of_Votes=$result->num_rows;
        return $No_of_Votes;
    }

    function sort_result($VP){
        include 'connect.php';
        $table_check="SHOW TABLES LIKE '$VP'";
        $table=$conn->query($table_check);
        $Query="SELECT * FROM jcr_result WHERE Position='$VP'";
        $data = $conn->query($Query);
        $get = $data->fetch_assoc();
        if ($table->num_rows>0 and $get !== null){
            if ("Multi-Voting"==$get['Type']){
                $results = json_decode($get['Results']);
                $v= str_replace("_"," ",substr($VP,4));
                echo<<<EOT
                    <br>
                        <div class="results-grid">
                            <!-- Candidates Results -->
                            <div class="results-list">
                                    <h3>$v</h3>
                    <br>
                EOT;
                foreach($results as $Pin){
                    $per = (no_of_votes() != 0) ? number_format(($Pin->results / no_of_votes()) * 100, 2) : 0;
                    echo<<<EOT
                        <div class="candidate-result">
                            <img class="candidate-avatar" src = {$Pin->image}>
                            <div class="candidate-info">
                                <div class="candidate-name">
                                    {$Pin->name}
                                </div>
                                <div class="vote-bar">
                                    <div class="vote-fill" style="width: $per%"></div>
                                </div>
                                <div class="vote-stats">
                                    <span>{$Pin->results} votes</span>
                                    <span>$per%</span>
                                </div>
                            </div>
                        </div>
                    EOT;
                }
                echo <<<EOT
                        </div>
                    </div>
                    <br>
                EOT;
            }
            elseif ("Referendum"==$get['Type']) {
                $results = json_decode($get['Results']);
                $v= str_replace("_"," ",substr($VP,4));
                echo<<<EOT
                    <br>
                        <div class="results-grid">
                            <!-- Candidates Results -->
                            <div class="results-list">
                                    <h3>$v</h3>
                    <br>
                EOT;
                foreach($result as $Pin){
                    $pery = (no_of_votes() != 0) ? number_format(($Pin->results_y / no_of_votes()) * 100, 2) : 0;
                    $pern = (no_of_votes() != 0) ? number_format(($Pin->results_n / no_of_votes()) * 100, 2) : 0;
                    echo<<<EOT
                            <div class="candidate-result">
                                <img class="candidate-avatar" src = {$Pin->image}>
                                <div class="candidate-info">
                                    <div class="candidate-name">
                                        {$Pin->name}
                                    </div>
                                    <div class="vote-bar">
                                        <div class="vote-fill" style="width: $pery%"></div>
                                    </div>
                                    <div class="vote-stats">
                                        <span>{$Pin->results_y} YES</span>
                                        <span>$pery%</span>
                                    </div>
                                    <div class="vote-bar">
                                        <div class="vote-fill" style="width: $pern%"></div>
                                    </div>
                                    <div class="vote-stats">
                                        <span>{$Pin->results_n} NO</span>
                                        <span>$pern%</span>
                                    </div>
                                </div>
                            </div>
                    EOT;
                }
                echo <<<EOT
                        </div>
                    </div>
                    <br>
                EOT;
            }
        }else{
            $v= str_replace("_"," ",substr($VP,4));
            echo<<<EOT
                    <br>
                        <div class="results-grid">
                            <!-- Candidates Results -->
                            <div class="results-list">
                                    <h3>$v</h3>
                    <br>
                    <h5 align='center'>No results yet!!!</h5><br>
                    </div>
                </div>
                EOT;
        }
    }
?>

<script>
    var list = [];
    var list_choice = [];
    function go(post){
        const c =document.querySelector('input[name="choice"]:checked').value;
        console.log(c);
        if (!c.includes("@")){
            const j = document.getElementsByClassName(post)[c[0]-1].defaultValue;
            console.log(j);
            list_choice.push([post,j,c.slice(1)]);
            // console.log(list_choice);
            choice(list_choice);
            
            document.getElementById("j"+post).src = c.slice(1);
            var new_choice = [post,c[0]];    
        }
        else{
            let [name,yn,image] = c.split("@");
            if (yn == 1){
                var ny = "YES";
            }
            else{
                var ny = "NO";
            }
            const j = name+`    (${ny})`;
            console.log(j);
            list_choice.push([post,j,image]);
            // console.log(list_choice);
            choice(list_choice);

            document.getElementById("j"+post).src = image;
            var new_choice = [post,yn];
        }
        list.push(new_choice);
        // console.log(list);
        save(post);
    }

    function save(post){
        const php_list = JSON.stringify(list);
        document.getElementById('array').value=php_list;
        document.getElementById(post).style.display="none";
        const sav =`<h3>Saved !</h3>`;
        document.getElementById(post).insertAdjacentHTML("afterend",sav);
        progress = document.getElementsByClassName('progress');
        buttons = document.getElementsByClassName('submit');
        for (const prog of progress){
            prog.value = list.length;
            console.log(list.length);
        }
        if (list.length == progress[0].max){
            for (button of buttons){
                button.disabled = false;
            }
        }
        else{
            for (button of buttons){
                button.disabled = true;
            }
        }
    }

    function choice(list_choice){
        var html = [];
        for (let n = 0; n < list_choice.length; n++){
            html.push(`<div class="summary-position">
                            <h4 class="position-name">${list_choice[n][0].substring(4).replace("_", " ")}</h4>
                            <div class="candidates-row">
                                <img src=${list_choice[n][2]} class="candidate-img" width="50" height="50" alt="Candidate"/>
                                <h5 class="candidate-name">${list_choice[n][1]}</h5>
                            </div>
                        </div>`);
        }
        document.getElementById('summary').innerHTML = html;
    }
    
</script>