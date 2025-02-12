<?php
require "vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->SMTPAuth = true;

$mail->Host = 'smtp.gmail.com';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

$mail->Username = "josephkuttor730@gmail.com";
$mail->Password = "ooupcvqmnwkzwjfn";

include 'connect.php';

if (isset($_POST['Report'])){
    require_once('C:/tcpdf/tcpdf.php');
    $pdf=new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    $pdf->AddPage();
    $pdf->SetFont('Helvetica', '', 12,);
    $year = date('Y');

    $pdf->Write(5,'UNIVERSITY OF MINES AND TECHONOLOGY, ESSIKADO
SCHOOL OF RAILWAY AND INFRUSTRACTURE DEVELOPMENT',1,);
    $pdf -> Ln();
    $pdf -> Ln();
    $pdf->Cell(0, 10, "UMAT ELECTORAL COMMISSION, JCR REPORT($year).", 1, 1, 'C');
    $pdf -> Ln();

    $pdf->Cell(40, 10, 'Session', 0, 0, 'C');
    $pdf->Cell(70, 10, '', 0, 0, 'C');
    $pdf->Cell(60, 10, 'Voters', 0, 1, 'C');

    $pdf->Cell(20, 10, 'Start:', 0, 0, 'L');
    $pdf->Cell(20, 10, date('Y-m-d H:i'), 0, 0, 'L');
    $pdf->Cell(70, 10, '', 0, 0, 'C');
    $pdf->Cell(40, 10, 'Registered Voters:', 0, 0, 'L');
    $pdf->Cell(20, 10, $_POST['voters']."     100%", 0, 1, 'L');

    $cast = ($_POST['voted']/$_POST['voters'])*100;
    $pcast = number_format($cast,2);

    $pdf->Cell(20, 10, 'End:', 0, 0, 'L');
    $pdf->Cell(20, 10, date('Y-m-d H:i'),0, 0, 'L');
    $pdf->Cell(70, 10, '', 0, 0, 'C');
    $pdf->Cell(40, 10, 'Votes Cast:', 0, 0, 'L');
    $pdf->Cell(20, 10, $_POST['voted']."   " .$pcast ."%", 0, 1, 'L');

    $pdf -> Ln();
    $pdf->Cell(0, 10, 'RESULTS', 0, 0, 'C');

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
    $posts=get_post();

    function sort_result($pdf,$VP){
        include 'connect.php';
        $table_check="SHOW TABLES LIKE '$VP'";
        $table=$jconn->query($table_check);
        if ($table->num_rows>0){
            $pdf -> Ln();
            //$pdf -> Ln();
            $pdf->Cell(0, 10, strtoupper($VP), 0, 1, 'C');
            $pdf->Cell(50, 10, 'PASSPORT', 0, 0, 'C');
            $pdf->Cell(80, 10, 'NAME', 0, 0, 'C');
            $pdf->Cell(50, 10, 'RESULT:  Fig.         Per      ', 0, 1, 'C');
            $getcandidates="SELECT * FROM candidate WHERE Post='$VP'";
            $result=$jconn->query($getcandidates);
            $getPost="SELECT * FROM post WHERE Post='$VP'";
            $format=$jconn->query($getPost);
            $row=$format->fetch_assoc();
            $n=1;
            $N=2;
            $votes="SELECT * FROM $VP";
            $votes_n=$jconn->query($votes);
            $No_votes=$votes_n->num_rows;

            if ("Multi-Voting"==$row['Type']){
                foreach($result as $Pin){
                    $getresult="SELECT * FROM $VP WHERE Candidate='$Pin[Full_Name]'";
                    $poll=$jconn->query($getresult);
                    $pollC=$poll->num_rows;
                    $pdf->Cell(50, 35, $pdf->Image($Pin['Image'],'','',40,30), 0, 0, 'C');
                    $pdf->Cell(80, 35, $Pin['Full_Name'], 0, 0, 'C');
                    $pdf->Cell(50, 35, $pollC.'          '.number_format(($pollC/$No_votes)*100,2).'%', 0, 0, 'R');
                    $pdf->Ln();
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
                    $pdf->Cell(50, 35, $pdf->Image($Pin['Image'],'','',40,30), 0, 0, 'C');
                    $pdf->Cell(80, 35, $Pin['Full_Name'], 0, 0, 'C');
                    $pdf->MultiCell(50, 35, '

Yes:         '.$pollC.'        '.number_format(($pollC/$No_votes)*100,2).'%
No:        '.$pollC2.'        '.number_format(($pollC2/$No_votes)*100,2).'%', 0, 'C');
                    $pdf->Ln();
                }
            }
        }
    }

    foreach($posts as $post){
        sort_result($pdf,$post['POST']);   
    }

    // $pdf->Cell(0, 10, "VOTER'S FEEDBACK", 0, 1, 'C');
    //     $select="SELECT * FROM Feedback";
    //     $sel=$jconn->query($select);
    //     $n=0;
    //     foreach ($sel as $info){
    //         $n+=1;
    //         if (!empty($info['Feedback'])){
    //             $pdf->MultiCell(0, 10,'   '.$n.'. '. $info['Feedback'],0,'L');
    //             $pdf->Ln();
    //             }
    //     }
    // $pdf->Ln();

    $pdf->Cell(0, 10, "EC STATEMENT", 0, 1, 'C');
        $select="SELECT * FROM ec_statement";
        $sel=$jconn->query($select);
        foreach ($sel as $info){
            if (!empty($info['Title'])){
                $pdf->Cell(0, 10,$info['Title'],0,1,'C');
            }
            if (!empty($info['Statement'])){
                $pdf->Cell(0, 10,$info['Statement'],0,1,'L');
            }
        }

    $pdf->Output('example.pdf','I');
}

if (isset($_POST['Reset'])){
    include 'OTP.php';
}
if (isset($_POST['verify'])){
    session_start();
    $expected_otp = $_POST['otp1'].$_POST['otp2'].$_POST['otp3'].$_POST['otp4'];
    if ($_SESSION['otp']==$expected_otp){    
        include 'connect.php';
        $getPost="SELECT POST FROM post";
        $format=$jconn->query($getPost);

        $jconn->query("DELETE FROM votes");
        $jconn->query("DELETE FROM candidate");
        $jconn->query("DELETE FROM ec_statement");
        $jconn->query("DELETE FROM feedback");
        $jconn->query("DELETE FROM nb");

        foreach($format as $Pin){
            $list[]=$Pin;   
        }
        //print_r($list);
        foreach($list as $li){
            if(!$jconn->query("DELETE FROM $li[POST]")){
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

// if (isset($_POST['Backup'])){
//     echo "Fred";
//     $backup_file = 'backup.sql';
//     // $command = "mysqldump -h $host -u $user -p $pass $dbs > $backup_file";
//     // exec($command);
//     $table = array();
//     $result = $con->query("SHOW TABLES");

//     while ($row = $result->fetch_row()) {
//         $tables[] = $row[0];
//     }

//     $fp = fopen($backup_file, 'w');

//     foreach ($tables as $table){
//         $result = $con->query("SELECT * FROM $table");

//         $fields = array();
//         $fields_num = $result->field_count;

//         for ($i = 0; $i < $fields_num; $i++){
//             $field = $result->fetch_field();
//             $fields = $field->name;
//         }

//         fwrite($fp, "DROP TABLE IF EXISTS $table;\n");
//         fwrite($fp, "CREATE TABLE $table;\n");

//         for($i = 0; $i < $fields_num; $i++){
//             $fields = $result->fetch_field();
//             fwrite($fp, "$field->name $field->type");
//             if ($i < $fields_num -  1){
//                 fwrite($fp, ",");
//             }
//             fwrite($fp, "\n");
//         }

//         fwrite($fp, ");\n");
//         fwrite($fp, "INSERT INTO $table VALUES ");

//         while ($row = $result->fetch_row()) {
//             $entry = '';
//             foreach ($row as $value){
//                 $entry .= "'$value',";
//             }
//             $entry = substr($entry, 0,-1);
//             fwrite($fp, "($entry),\n");
//         }
//         fwrite($fp, ";\n\n");
//     }
//     fclose($fp);
// }

if (isset($_POST['jaddP'])){
    //$postId=$_POST['postId'];
    $postName=$_POST['postName'];
    $postType=$_POST['postType'];

    $checkPost="SELECT * FROM post where Post='$postName'";
    $result=$jconn->query($checkPost);
    if($result->num_rows>0){
        echo "<script>alert ('Post Already Exist!')</script>";
    }
    else{
        $insertQuery="INSERT INTO post(Post,Type)
        VALUES('$postName','$postType')";

        if($jconn->query($insertQuery)==True){
            header("location: ad.jcr.php?Login=success");
            echo "<script>FpostM();
                alert ('Saved');</script>";
        }
        else{
            echo "Error:".$jconn->error;
        }
    }
}

if (isset($_POST['jeditP'])){
    $postName=$_POST['postName'];
    $postType=$_POST['postType'];
    $selector=$_POST['selector'];

    $updateC="UPDATE post SET Post='$postName', Type='$postType' WHERE Post_id='$selector'";
    $save=$jconn->query($updateC);
    if ($save){
        header("location: ad.jcr.php?Login=success");
        echo "<script>FpostM();
            alert ('Saved');</script>";
    }
}

if (isset($_POST['jdeletP'])){
    $selector=$_POST['selector'];
        $deleteC="DELETE FROM post WHERE Post_id='$selector'";
        $del=$jconn->query($deleteC);
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

    $checkPost="SELECT * FROM candidate where Full_name='$Cname' or Index_No='$Cindex' or Reference_No='$Creference'";
    $result=$jconn->query($checkPost);
    if($result->num_rows>0){
        echo "<script>alert ('Candidate Already Exist!')</script>";
        //header("location: ad.jcr.php?Login=success");
    }
    else{
        if(in_array($image_type,$allowedext)){
            move_uploaded_file($image_tmp,$image_path);
            $insertQuery="INSERT INTO candidate(Index_No,Full_Name,Reference_No,Post,Image)
            VALUES('$Cindex','$Cname','$Creference','$post','$image_path')";

            if($jconn->query($insertQuery)==True){
                // include 'ad.jcr.php?Login=success';
                header("location: ad.jcr.php?Login=success");
                echo "<script>FcandidateM();
                    alert ('Candidate Input Sucessfull');</script>";
            }
            else{
                echo "Error:".$jconn->error;
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
        $updateC="UPDATE candidate SET Index_No='$Cindex',Full_Name='$Cname',Reference_No='$Creference',Post='$post',Image='$image_path' WHERE Index_No='$selector'";
        $save=$jconn->query($updateC);
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
        $deleteC="DELETE FROM candidate WHERE Index_No='$selector'";
        $del=$jconn->query($deleteC);
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
            $insertQuery="INSERT INTO nb (Message,File)
            VALUES('$msg','$file_path')";

            if($jconn->query($insertQuery)==True){
                header("location: ad.jcr.php?Login=success");
                echo "<script>FNB();</script>";
                echo "<script>alert ('Message Input Sucessfull');</script>";
            }
            else{
                echo "Error:".$jconn->error;
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
        $updateC="UPDATE nb SET Message='$msg',File='$$file_path' WHERE SN='$selector'";
        $save=$jconn->query($updateC);
            if ($save){
                echo "<script>alert ('Saved')</script>";
                //header("location: ad.jcr.php?Login=success");
            }
    }

    if (isset($_POST['jdeleteNB'])){
            $selector=$_POST['selector'];
            $delete="DELETE FROM nb WHERE SN='$selector'";
            $del=$jconn->query($delete);
            if ($del){
                header("location: ad.jcr.php?Login=success");
                echo "<script>FNB();
                alert ('Deletion Sucessful!')</script>";
            }
            else{
                echo "<script>alert ('There is an error/nPlease trobleshoot amd try again.')</script>";
            }
        }
    
        function get_temp_link($stmail,$stun,$url="jcr.poll.php",$ex=3600){
            session_start();
            $token = bin2hex($stmail);
            $time = time();
            $ex_t = $time + $ex;
            $hash = hash_hmac('sha256',$token . $time, $stun);
            $temp_link = $url.'?token='.$token.'&time='.$time.'&hash='.$hash;
            return $temp_link;
        }
    
    if (isset($_POST['jsst'])){
        $table_check="SHOW TABLES LIKE 'session'";
        $table=$jconn->query($table_check);
        if ($table->num_rows>0){
            $insert="UPDATE session SET session='start' WHERE session='start' OR session='stop'";
            
                    $sql="SELECT Student_Email FROM voters";
                    $result=$con->query($sql);
                    $email=array();
                    while ($row=$result->fetch_assoc()) {
                        array_push($email,$row['Student_Email']);
                    }

                    $sender="umat-srid";
                    $Smail="josephkuttor730@gmail.com";
                    $subject = "UMAT JCR";

                    foreach ($email as $to){
                        $msg=random_int(1000,9999);
                        $key="SRID.JCR.".$msg;

                        $_SESSION['jlink']=get_temp_link($to,$key);
                            
                        $mail->setFrom($Smail,$sender);
                        $mail->addAddress($to);

                        $mail->Subject = $subject;
                        $mail->Body ="<p>Click on this link: <a href='$_SESSION[jlink]'> JCR Poll </a> to take part in the SRC election.</p>
                        <p>This ' $key '  would be your unique code for the election.</p>
                        <p>Use your student email account and the unique code to login and cast your vote.</p>
                        <p>You would receive an email after done voting to affirm your vote has been cast successfully.</p>";
                        
                        $mail->isHTML(true);

                        $sql="SELECT * FROM sent_links WHERE Student_Email='$to'";
                        $result=$jconn->query($sql);
                        if(!$result->num_rows>0){

                            if($mail->send()) {
                                $jconn->query($insert);
                                echo "Mail successful!";
                                $insertQuery="INSERT INTO sent_links(Student_Email,Link_Sent)
                                VALUES('$to','$_SESSION[jlink]')";
                                $jconn->query($insertQuery);
                            }else {
                                echo "Mail failed!";
                            }
                            
                        }
                    }
                    header("location: ad.jcr.php?Login=success");
        }else{
            $create_table="CREATE TABLE session(
                session VARCHAR(5) NOT NULL)";
            $jconn->query($create_table);
            header("location: ad.jcr.php?Login=success");
        }
    }

    if (isset($_POST['jssp'])){

        $table_check="SHOW TABLES LIKE 'session'";
        $table=$jconn->query($table_check);
        if ($table->num_rows>0){
            $insert="UPDATE session SET session='stop' WHERE session='start' OR session='stop'";
            $jconn->query($insert);
            header("location: ad.jcr.php?Login=success");
        }else{
            $create_table="CREATE TABLE session(
                session VARCHAR(5) NOT NULL)";
            $jconn->query($create_table);
            header("location: ad.jcr.php?Login=success");
        }
    }

    if (isset($_POST['submit_feed'])){
        $feedback=$_POST['feedback'];
        
        $insertQuery="INSERT INTO feedback(Feedback)
            VALUES('$feedback')";
    
        if($jconn->query($insertQuery)==True){
            header("location: jvhome.php?Login=success");
            echo "<script>alert ('Submited');</script>";
        }
        else{
            echo "Error:".$jconn->error;
        }
    }

    if (isset($_POST['submitST'])){
        $title=$_POST['title'];
        $statement=$_POST['statement'];
    
        $del_statement="DELETE FROM ec_statement";
        $result=$jconn->query($del_statement);
        $insertQuery="INSERT INTO ec_statement(Title,Statement)
            VALUES('$title','$statement')";
    
        if($jconn->query($insertQuery)==True){
            header("location: ad.jcr.php?Login=success");
            echo "<script>alert ('Submited');</script>";
        }
        else{
            echo "Error:".$jconn->error;
        }
    }

    if (isset($_POST['deleteST'])){
        $deleteC="DELETE FROM ec_statement";
        $jconn->query($deleteC);
        header("location: ad.jcr.php?Login=success");
    }

    if (isset($_POST['submit_feed'])){
        $feedback=$_POST['feedback'];
        
        $insertQuery="INSERT INTO feedback(Feedback)
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
    
        $del_statement="DELETE FROM ec_statement";
        $result=$conn->query($del_statement);
        $insertQuery="INSERT INTO ec_statement(Title,Statement)
            VALUES('$title','$statement')";
    
        if($conn->query($insertQuery)==True){
            header("location: ad.src.php?Login=success");
            echo "<script>alert ('Submited');</script>";
        }
        else{
            echo "Error:".$conn->error;
        }
    }

    if (isset($_POST['deleteST'])){
        $deleteC="DELETE FROM ec_statement";
        $conn->query($deleteC);
        header("location: ad.src.php?Login=success");
    }

?>