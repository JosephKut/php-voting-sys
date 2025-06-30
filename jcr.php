<?php
include 'connect.php';
include 'resources.php';

$get = "SELECT * FROM jcr_session";
$session = $conn->query($get);
$ses = $session->fetch_assoc();

// --- Helper Functions ---

function get_post($conn) {
    $getPost = "SELECT POST FROM jcr_post";
    $format = $conn->query($getPost);
    $list = [];
    foreach ($format as $Pin) {
        $list[] = $Pin;
    }
    return $list;
}

function sort_result($pdf, $VP, $conn) {
    $table_check = "SHOW TABLES LIKE '$VP'";
    $Query = "SELECT * FROM jcr_result WHERE Position='$VP'";
    $data = $conn->query($Query);
    $get = $data->fetch_assoc();
    $table = $conn->query($table_check);

    if ($table->num_rows > 0) {
        $v = str_replace("_", " ", substr($VP, 4));
        // Position Header
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(0, 10, strtoupper($v), 0, 1, 'C', 1);
        
        // Table header
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(55, 8, 'PASSPORT', 1, 0, 'C', 1);
        $pdf->Cell(80, 8, 'NAME', 1, 0, 'C', 1);
        $pdf->Cell(55, 8, 'RESULT:  Fig.         Per', 1, 1, 'C', 1);
        $pdf->Ln();
        $votes = "SELECT * FROM $VP";
        $votes_n = $conn->query($votes);
        $No_votes = $votes_n->num_rows;

        if ("Multi-Voting" == $get['Type']) {
            $results = json_decode($get['Results']);
            foreach ($results as $Pin) {
                $pdf->Cell(50, 35, $pdf->Image($Pin->image, '', '', 40, 30), 0, 0, 'C');
                $pdf->Cell(80, 35, $Pin->name, 0, 0, 'C');
                $pdf->Cell(50, 35, $Pin->results . '          ' . (($No_votes != 0) ? number_format(($Pin->results / $No_votes) * 100, 2) : 0) . '%', 0, 0, 'R');
                $pdf->Ln();
            }
        } elseif ("Referendum" == $get['Type']) {
            $results = json_decode($get['Results']);
            foreach ($results as $Pin) {
                $pdf->Cell(50, 35, $pdf->Image($Pin->image, '', '', 40, 30), 0, 0, 'C');
                $pdf->Cell(80, 35, $Pin->name, 0, 0, 'C');
                $pdf->MultiCell(50, 35,
                    "Yes:  {$Pin->results_y}   " . (($No_votes != 0) ? number_format(($Pin->results_y / $No_votes) * 100, 2) : 0) . "%\n" .
                    "No:   {$Pin->results_n}   " . (($No_votes != 0) ? number_format(($Pin->results_n / $No_votes) * 100, 2) : 0) . "%",
                    0, 'C'
                );
                $pdf->Ln();
            }
        }
    }
}

// --- Main PDF Generation ---

if (isset($_POST['Report'])) {
    require_once('C:/tcpdf/tcpdf.php');
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // --- Cover Page ---
    $pdf->AddPage();
    // Add logo if available
    // $pdf->Image('path/to/logo.png', 80, 20, 50, '', '', '', '', false, 300);

    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->Ln(30);
    $pdf->Cell(0, 15, "UNIVERSITY OF MINES AND TECHNOLOGY", 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 16);
    $pdf->Cell(0, 10, "School of Railway and Infrastructure Development", 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->SetTextColor(15, 146, 221);
    $pdf->Cell(0, 12, "JCR ELECTION REPORT", 0, 1, 'C');
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('helvetica', '', 14);
    $pdf->Cell(0, 10, "Year: " . date('Y'), 0, 1, 'C');
    $pdf->Ln(40);
    $pdf->SetFont('helvetica', 'I', 12);
    $pdf->Cell(0, 10, "Prepared by: UMAT Electoral Commission", 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "Date: " . date('F d, Y'), 0, 1, 'C');

    // Add a new page for the report content
    $pdf->AddPage();

    // --- Session and Voters Info ---
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetFillColor(15, 146, 221);
    $pdf->SetTextColor(255,255,255);
    $pdf->Cell(0, 12, "SESSION SUMMARY", 0, 1, 'C', 1);
    $pdf->SetTextColor(0,0,0);
    $pdf->Ln(4);

    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(40, 8, 'Session:', 0, 0, 'L');
    $pdf->Cell(60, 8, $ses['begin'] . " - " . $ses['end'], 0, 1, 'L');
    $pdf->Cell(40, 8, 'Registered Voters:', 0, 0, 'L');
    $pdf->Cell(60, 8, $_POST['voters'] . " (100%)", 0, 1, 'L');
    $cast = ($_POST['voters'] != 0) ? number_format(($_POST['voted'] / $_POST['voters']) * 100, 2) : 0;
    $pcast = number_format($cast, 2);
    $pdf->Cell(40, 8, 'Votes Cast:', 0, 0, 'L');
    $pdf->Cell(60, 8, $_POST['voted'] . " ($pcast%)", 0, 1, 'L');
    $pdf->Ln(8);

    // --- Results Section ---
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetFillColor(101, 245, 120);
    $pdf->Cell(0, 12, 'ELECTION RESULTS', 0, 1, 'C', 1);
    $pdf->Ln(2);

    $posts = get_post($conn);
    foreach ($posts as $post) {
        // Table body
        $pdf->SetFont('helvetica', '', 11);
        sort_result($pdf, $post['POST'], $conn);
        $pdf->Ln(3);
    }

    // --- EC Statement Section ---
    $pdf->AddPage();
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetFillColor(15, 146, 221);
    $pdf->SetTextColor(255,255,255);
    $pdf->Cell(0, 12, "EC STATEMENT", 0, 1, 'C', 1);
    $pdf->SetTextColor(0,0,0);
    $pdf->Ln(4);

    $select = "SELECT * FROM jcr_ec_statement";
    $sel = $conn->query($select);
    $pdf->SetFont('helvetica', '', 12);
    foreach ($sel as $info) {
        if (!empty($info['Title'])) {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 8, $info['Title'], 0, 1, 'C');
        }
        if (!empty($info['Statement'])) {
            $pdf->SetFont('helvetica', '', 12);
            $pdf->MultiCell(0, 8, $info['Statement'], 0, 'L');
            $pdf->Ln(2);
        }
    }

    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 12, "_________________________", 0, 1, 'R', 0);
    $pdf->Cell(0, 12, "_________________________", 0, 1, 'R', 0);
    $pdf->Cell(0, 12, "_________________________", 0, 1, 'R', 0);
    $pdf->Ln(4);

    // --- Output PDF ---
    $pdf->Output('jcr_report.pdf', 'I');
}

if (isset($_POST['Reset'])){
    $class = "jcr";
    include 'OTP.php';
}
if (isset($_POST['verify'])){
    session_start();
    $expected_otp = $_POST['otp1'].$_POST['otp2'].$_POST['otp3'].$_POST['otp4'];
    if ($_SESSION['otp']==$expected_otp){    
        include 'connect.php';
        $getPost="SELECT POST FROM jcr_post";
        $format=$conn->query($getPost);

        $conn->query("DELETE FROM jcr_votes");
        $conn->query("DELETE FROM jcr_candidate");
        $conn->query("DELETE FROM jcr_ec_statement");
        $conn->query("DELETE FROM jcr_feedback");
        $conn->query("DELETE FROM jcr_nb");
        $conn->query("DELETE FROM jcr_sent_links");

        foreach($format as $Pin){
            $list[]=$Pin;   
        }
        //print_r($list);
        foreach($list as $li){
            if(!$conn->query("DELETE FROM $li[POST]")){
                $hb;
            }
        }
        header("location: ad.jcr.php?Login=success");
    }else{
        echo <<<EOT
            <script>
                alert "OTP Invalid!";
            </script>
        EOT;
    }
}

if (isset($_POST['jaddP'])){
    //$postId=$_POST['postId'];
    $postName = "jcr_".str_replace(" ","_", trim($_POST['postName']));
    $postType=$_POST['postType'];

    $checkPost="SELECT * FROM jcr_post where Post='$postName'";
    $result=$conn->query($checkPost);
    if($result->num_rows>0){
        echo "<script>alert ('Post Already Exist!')</script>";
    }
    else{
        $insertQuery="INSERT INTO jcr_post(Post,Type)
        VALUES('$postName','$postType')";

        if($conn->query($insertQuery)==True){
            header("location: ad.jcr.php?Login=success");
            echo "<script>FpostM();
                alert ('Saved');</script>";
        }
        else{
            echo "Error:".$conn->error;
        }
    }
}

if (isset($_POST['jeditP'])){
    $postName=$_POST['postName'];
    $postType=$_POST['postType'];
    $selector=$_POST['selector'];

    $updateC="UPDATE jcr_post SET Post='$postName', Type='$postType' WHERE Post_id='$selector'";
    $save=$conn->query($updateC);
    if ($save){
        header("location: ad.jcr.php?Login=success");
        echo "<script>FpostM();
            alert ('Saved');</script>";
    }
}

if (isset($_POST['jdeletP'])){
    $selector=$_POST['selector'];
        $deleteC="DELETE FROM jcr_post WHERE Post_id='$selector'";
        $del=$conn->query($deleteC);
        if ($del){
            header("location: ad.jcr.php?Login=success");
            echo "<script>FpostM();
                alert ('Deletion Sucessfull');</script>";
        }
    }

if (isset($_POST['jaddC'])){
    $Cname=$_POST['Cname'];
    $Cindex=$_POST['Cindex'];
    $Creference=$_POST['Creference'];
    $post=$_POST['postC'];
    $image=$_FILES['Image'];
    $image_name=$image['name'];
    $image_type=$image['type'];
    $image_size=$image['size'];
    $image_tmp=$image['tmp_name'];
    $allowedext=array("image/jpg","image/png","image/jpeg","image/gif","image/PNG");

    $upload_dir="uploads/";
    $image_path=$upload_dir . $image_name;

    $checkPost="SELECT * FROM jcr_candidate where Full_name='$Cname' or Index_No='$Cindex' or Reference_No='$Creference'";
    $result=$conn->query($checkPost);
    if($result->num_rows>0){
        echo "<script>alert ('Candidate Already Exist!')</script>";
        //header("location: ad.jcr.php?Login=success");
    }
    else{
        if(in_array($image_type,$allowedext)){
            move_uploaded_file($image_tmp,$image_path);
            $insertQuery="INSERT INTO jcr_candidate(Index_No,Full_Name,Reference_No,Post,Image)
            VALUES('$Cindex','$Cname','$Creference','$post','$image_path')";

            if($conn->query($insertQuery)==True){
                // include 'ad.jcr.php?Login=success';
                header("location: ad.jcr.php?Login=success");
                echo "<script>FcandidateM();
                    alert ('Candidate Input Sucessfull');</script>";
            }
            else{
                echo "Error:".$conn->error;
            }
        }
        else{
            header("location: ad.jcr.php?Login=success");
            echo "<script>FcandidateM();</script>";
            echo "<script>alert ('Image Type Invalid!')</script>";
        }
    }
}

if (isset($_POST['jeditC'])){
    $Cname=$_POST['Cname'];
    $Cindex=$_POST['Cindex'];
    $Creference=$_POST['Creference'];
    $post=$_POST['postC'];
    $image=$_FILES['Image'];
    $selector=$_POST['selector'];
    $image_name=$image['name'];
    $image_type=$image['type'];
    $image_size=$image['size'];
    $image_tmp=$image['tmp_name'];
    $allowedext=array("image/jpg","image/png","image/jpeg","image/gif","image/PNG");

    $upload_dir="uploads/";
    $image_path=$upload_dir . $image_name;

    if(in_array($image_type,$allowedext)){
        move_uploaded_file($image_tmp,$image_path);
        $updateC="UPDATE jcr_candidate SET Index_No='$Cindex',Full_Name='$Cname',Reference_No='$Creference',Post='$post',Image='$image_path' WHERE Index_No='$selector'";
        $save=$conn->query($updateC);
        if ($save){
            header("location: ad.jcr.php?Login=success");
            echo "<script>FcandidateM();</script>";
            echo "<script>alert ('Saved')</script>";
        }
    }
    else{
        echo "<script>alert ('Image Type Invalid!')</script>";
    }
}

if (isset($_POST['jdeletC'])){
    $selector=$_POST['selector'];
        $deleteC="DELETE FROM jcr_candidate WHERE Index_No='$selector'";
        $del=$conn->query($deleteC);
        if ($del){
            header("location: ad.jcr.php?Login=success");
            echo "<script>FcandidateM();</script>";
            echo "<script>alert ('Deletion Sucessful!')</script>";
        }
        else{
            echo "<script>alert ('There is an error')</script>";
        }
    }

    if(isset($_GET['jcr_chart'])){
        header("location: jcr.chart.php");
    }

    if (isset($_POST['jaddNB'])){
            $msg=$_POST['msg'];
            $file=$_FILES['file'];
            print_r($file);
            $file_name=$file['name'];
            $file_type=$file['type'];
            $file_size=$file['size'];
            $file_tmp=$file['tmp_name'];
            $allowedext=array("image/jpg","image/png","image/jpeg","image/gif","image/PNG");
        
            $upload_dir="uploads/";
            $file_path=$upload_dir . $file_name;

            move_uploaded_file($file_tmp,$file_path);
            $insertQuery="INSERT INTO jcr_nb (Message,File)
            VALUES('$msg','$file_path')";

            if($conn->query($insertQuery)==True){
                header("location: ad.jcr.php?Login=success");
                echo "<script>FNB();</script>";
                echo "<script>alert ('Message Input Sucessfull');</script>";
            }
            else{
                echo "Error:".$conn->error;
            }
    }

    if (isset($_POST['jupdateNB'])){
        $msg=$_POST['msg'];
        $file=$_FILES['file'];
        $file_name=$file['name'];
        $file_type=$file['type'];
        $file_size=$file['size'];
        $file_tmp=$file['tmp_name'];
        $allowedext=array("image/jpg","image/png","image/jpeg","image/gif","image/PNG");
    
        $upload_dir="uploads/";
        $file_path=$upload_dir . $file_name;

        move_uploaded_file($file_tmp,$file_path);
        $updateC="UPDATE jcr_nb SET Message='$msg',File='$$file_path' WHERE SN='$selector'";
        $save=$conn->query($updateC);
            if ($save){
                echo "<script>alert ('Saved')</script>";
                //header("location: ad.jcr.php?Login=success");
            }
    }

    if (isset($_POST['jdeleteNB'])){
            $selector=$_POST['selector'];
            $delete="DELETE FROM jcr_nb WHERE SN='$selector'";
            $del=$conn->query($delete);
            if ($del){
                header("location: ad.jcr.php?Login=success");
                echo "<script>FNB();
                alert ('Deletion Sucessful!')</script>";
            }
            else{
                echo "<script>alert ('There is an error/nPlease trobleshoot amd try again.')</script>";
            }
        }
    
        function get_temp_link($stmail,$stun,$ex=14400,$url="jcr.poll.php"){
            include 'resources.php';
            // ...existing code...
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            // ...existing code...
            $token = bin2hex($stmail);
            $time = time();
            $ex_t = $time + $ex;
            $hash = hash_hmac('sha256',$token . $time, $stun);
            $temp_link = $Domain.$url.'?token='.$token.'&time='.$time.'&hash='.$hash;
            return $temp_link;
        }
    
    if (isset($_POST['jsst'])){
        $duration = $_POST['duration'];
        $duration_s = $duration * 3600;
        $table_check="SHOW TABLES LIKE 'jcr_session'";
        $table=$conn->query($table_check);
        if ($table->num_rows>0){
            include 'links.php';
            $time = date('Y-m-d H:i:s');
            $insert="UPDATE jcr_session SET status = 1, begin = '$time', duration = '$duration_s' WHERE session = 'start'";
            
                    $sql="SELECT Student_Email FROM voters";
                    $result=$conn->query($sql);
                    $email=array();
                    while ($row=$result->fetch_assoc()) {
                        array_push($email,$row['Student_Email']);
                    }

                    $From="umat-srid";
                    $Subject = "UMAT JCR";
                    $SuccessMsg = "sent";
                    $FailedMsg = "failed";

                    $i == 0;
                    function no_of_voters(){
                        include 'connect.php';
                        $getVoters="SELECT * FROM voters";
                        $result=$conn->query($getVoters);
                        $Voters=$result->num_rows;
                        return $Voters;
                    }
                    $voters = no_of_voters();
                    foreach ($email as $To){
                        $per = ($voters != 0) ? number_format(($i / $voters) * 100, 2) : 0;
                        echo <<<EOT
                            <script>
                                document.getElementById('links_sent').textContent="links sent: "$i;
                                document.getElementById('links_per').textContent="voters: "$per"%";
                                document.getElementById('links_per').style.width= $per%";
                            </script>
                        EOT;
                        $i++;
                        $msg=random_int(1000,9999);
                        $key="SRID.JCR.".$msg;

                        $_SESSION['jlink']=get_temp_link($To,$key,$duration_s);
                            
                        $Body ="<p>Click on this link: <a href='$_SESSION[jlink]'> JCR Poll </a> to take part in the JCR election.</p>
                        <p>This ' $key '  would be your unique code for the election.</p>
                        <p>Use your student email account and the unique code to login and cast your vote.</p>
                        <p>You would receive an email after done voting to affirm your vote has been cast successfully.</p>";
                        
                        $sql="SELECT * FROM jcr_sent_links WHERE Student_Email='$To'";
                        $result=$conn->query($sql);
                        if(!$result->num_rows>0){

                            echo "<script>
                                    console.log('Sending mail to $To');
                                </script>";

                            include 'mail.php';
                            // $sent = "<script> sent; </script>"; // Simulating email sent status

                            // if($sent == 1) {
                                $conn->query($insert);
                                echo "Mail successful!";
                                $insertQuery="INSERT INTO jcr_sent_links(Student_Email,Link_Sent)
                                VALUES('$To','$_SESSION[jlink]')";
                                $conn->query($insertQuery);
                            // }else {
                            //     echo "Mail failed!";
                            // }
                            
                        }
                    }
                    header("location: ad.jcr.php?Login=success");
        }else{
            $create_table="CREATE TABLE jcr_session(
                session VARCHAR(5),
                begin TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                end TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                status INT(1) NOT NULL,
                `release` INT(1) NOT NULL)
                duration INT(10)";
            $insert = "INSERT INTO jcr_session(session, begin, end, status, `release`)
                        VALUES ('start','','',0,0)";
            $conn->query($create_table);
            $conn->query($insert);
            header("location: ad.jcr.php?Login=success");
        }
    }

    if (isset($_POST['jssp'])){

        $table_check="SHOW TABLES LIKE 'jcr_session'";
        $table=$conn->query($table_check);
        if ($table->num_rows>0){
            $time = date('Y-m-d H:i:s');
            $insert="UPDATE jcr_session SET status = 0, end = '$time' WHERE session = 'start'";
            $conn->query($insert);
            header("location: ad.jcr.php?Login=success");
        }else{
            $create_table="CREATE TABLE jcr_session(
                session VARCHAR(5) DEFAULT 'start',
                begin TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                end TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                status INT(1) NOT NULL,
                release INT(1) NOT NULL)";
            $insert = "INSERT INTO jcr_session(session, begin, end, status, `release`)
                        VALUES ('start','00:00:00','00:00:00',0,0)";
            $conn->query($create_table);
            $conn->query($insert);
            header("location: ad.jcr.php?Login=success");
        }
    }

    if (isset($_POST['release_result'])){
        $get_s="SELECT * FROM jcr_session";
        $session=$conn->query($get_s);
        $ses=$session->fetch_assoc();
        if ($ses['release']== 1){
            $insert="UPDATE jcr_session SET `release` = 0 WHERE session = 'start'";
        }else{
            $insert="UPDATE jcr_session SET `release` = 1 WHERE session = 'start'";
        }
        $stmt = $conn->prepare($insert);
        $stmt->execute();
        $conn->query("DELETE FROM jcr_result");
        $getPost="SELECT POST FROM jcr_post";
        $format=$conn->query($getPost);
        $row=$format->fetch_assoc();
        foreach($format as $VP){
            
            $VP = $VP['POST'];
            $table_check="SHOW TABLES LIKE '$VP'";
            $table=$conn->query($table_check);
            if ($table->num_rows>0){
                $getcandidates="SELECT * FROM jcr_candidate WHERE Post='$VP'";
                $result=$conn->query($getcandidates);
                $getPost="SELECT * FROM jcr_post WHERE Post='$VP'";
                $format=$conn->query($getPost);
                $row=$format->fetch_assoc();
                $results = array();

                if ("Multi-Voting"==$row['Type']){
                    foreach($result as $Pin){
                        $getresult="SELECT * FROM $VP WHERE Candidate='$Pin[Full_Name]'";
                        $poll=$conn->query($getresult);
                        $pollC=$poll->num_rows;
                        $results[] = array(
                                            "name" => $Pin['Full_Name'],
                                            "image" => $Pin['Image'],
                                            "results" => $pollC
                                        );
                    }
                    print_r($results);
                }
                elseif ("Referendum"==$row['Type']) {
                    foreach($result as $Pin){
                        $getresult="SELECT * FROM $VP WHERE Candidate='$Pin[Full_Name]' AND Votes='1'";
                        $poll=$conn->query($getresult);
                        $pollC=$poll->num_rows;
                        $getresult2="SELECT * FROM $VP WHERE Candidate='$Pin[Full_Name]' AND Votes='2'";
                        $poll2=$conn->query($getresult2);
                        $pollC2=$poll2->num_rows;
                        $results[] = array(
                                            "name" => $Pin['Full_Name'],
                                            "image" => $Pin['Image'],
                                            "results_y" => $pollC,
                                            "results_n" => $pollC2
                                        );
                    }
                }
                $results = json_encode($results);
                $insertQuery="INSERT INTO jcr_result(Position,Results,Type)
                        VALUES('$VP','$results','$row[Type]')";
                $conn->query($insertQuery);
            }
        }
        header("location: ad.jcr.php?Login=success");
    }

    if (isset($_POST['submit_feed'])){
        $feedback=$_POST['feedback'];
        
        $insertQuery="INSERT INTO jcr_feedback(Feedback)
            VALUES('$feedback')";
    
        if($conn->query($insertQuery)==True){
            header("location: jvhome.php?Login=success");
            echo "<script>alert ('Submited');</script>";
        }
        else{
            echo "Error:".$conn->error;
        }
    }

    if (isset($_POST['submitST'])){
        $title=$_POST['title'];
        $statement=$_POST['statement'];
    
        $del_statement="DELETE FROM jcr_ec_statement";
        $result=$conn->query($del_statement);
        $insertQuery="INSERT INTO jcr_ec_statement(Title,Statement)
            VALUES('$title','$statement')";
    
        if($conn->query($insertQuery)==True){
            header("location: ad.jcr.php?Login=success");
            echo "<script>alert ('Submited');</script>";
        }
        else{
            echo "Error:".$conn->error;
        }
    }

    if (isset($_POST['deleteST'])){
        $deleteC="DELETE FROM jcr_ec_statement";
        $conn->query($deleteC);
        header("location: ad.jcr.php?Login=success");
    }

    if (isset($_POST['submit_feed'])){
        $feedback=$_POST['feedback'];
        
        $insertQuery="INSERT INTO jcr_feedback(Feedback)
            VALUES('$feedback')";
    
        if($conn->query($insertQuery)==True){
            header("location: vhome.php?Login=success");
            echo "<script>alert ('Submited');</script>";
        }
        else{
            echo "Error:".$conn->error;
        }
    }

    if (isset($_POST['submitST'])){
        $title=$_POST['title'];
        $statement=$_POST['statement'];
    
        $del_statement="DELETE FROM jcr_ec_statement";
        $result=$conn->query($del_statement);
        $insertQuery="INSERT INTO jcr_ec_statement(Title,Statement)
            VALUES('$title','$statement')";
    
        if($conn->query($insertQuery)==True){
            header("location: ad.jcr.php?Login=success");
            echo "<script>alert ('Submited');</script>";
        }
        else{
            echo "Error:".$conn->error;
        }
    }

    if (isset($_POST['deleteST'])){
        $deleteC="DELETE FROM jcr_ec_statement";
        $conn->query($deleteC);
        header("location: ad.jcr.php?Login=success");
    }

?>