<?php

include 'connect.php';
//include("func.php");

if (isset($_POST['Report'])){
    require_once('C:/tcpdf/tcpdf.php');
    $pdf=new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    $pdf->AddPage();
    $pdf->SetFont('Helvetica', '', 12,);
    $year = date('Y');

    // $pdf->StartTransform();
    // $pdf->Ellipse(75,75,50,50,'CNZ');
    // $pdf->Clip();
    // $pdf->Image('images/a.jpg',50,50,100,'','','','',false,300,'',false,false,0);
    // $pdf->StopTransform();
    $pdf->Write(5,'UNIVERSITY OF MINES AND TECHONOLOGY, ESSIKADO
SCHOOL OF RAILWAY AND INFRUSTRACTURE DEVELOPMENT',1,);
    $pdf -> Ln();
    $pdf -> Ln();
    $pdf->Cell(0, 10, "UMAT ELECTORAL COMMISSION, SRC REPORT($year).", 1, 1, 'C');
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
        $getPost="SELECT POST FROM src_post";
        $format=$conn->query($getPost);
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
        $table=$conn->query($table_check);
        if ($table->num_rows>0){
            $pdf -> Ln();
            //$pdf -> Ln();
            $pdf->Cell(0, 10, strtoupper($VP), 0, 1, 'C');
            $pdf->Cell(50, 10, 'PASSPORT', 0, 0, 'C');
            $pdf->Cell(80, 10, 'NAME', 0, 0, 'C');
            $pdf->Cell(50, 10, 'RESULT:  Fig.         Per      ', 0, 1, 'C');
            $getcandidates="SELECT * FROM src_candidate WHERE Post='$VP'";
            $result=$conn->query($getcandidates);
            $getPost="SELECT * FROM src_post WHERE Post='$VP'";
            $format=$conn->query($getPost);
            $row=$format->fetch_assoc();
            $n=1;
            $N=2;
            $votes="SELECT * FROM $VP";
            $votes_n=$conn->query($votes);
            $No_votes=$votes_n->num_rows;

            if ("Multi-Voting"==$row['Type']){
                foreach($result as $Pin){
                    $getresult="SELECT * FROM $VP WHERE Candidate='$Pin[Full_Name]'";
                    $poll=$conn->query($getresult);
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
                    $poll=$conn->query($getresult);
                    $pollC=$poll->num_rows;
                    $getresult2="SELECT * FROM $VP WHERE Candidate='$Pin[Full_Name]' AND Votes='2'";
                    $poll2=$conn->query($getresult2);
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
    //     $sel=$conn->query($select);
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
        $select="SELECT * FROM src_ec_statement";
        $sel=$conn->query($select);
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
        $getPost="SELECT POST FROM src_post";
        $format=$conn->query($getPost);

        $conn->query("DELETE FROM src_votes");
        $conn->query("DELETE FROM src_candidate");
        $conn->query("DELETE FROM src_ec_statement");
        $conn->query("DELETE FROM src_feedback");
        $conn->query("DELETE FROM src_nb");
        $conn->query("DELETE FROM src_sent_links");
        

        foreach($format as $Pin){
            $list[]=$Pin;   
        }
        foreach($list as $li){
            if(!$conn->query("DELETE FROM $li[POST]")){
                $hd;
            }
        }
        header("location: ad.src.php?Login=success");
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

//Add posts to database
if (isset($_POST['addP'])){
    //$postId=$_POST['postId'];
    $postName= str_replace(" ","_", str_trim($_POST['postName']));
    $postType=$_POST['postType'];

    $checkPost="SELECT * FROM src_post where Post='$postName'";
    $result=$conn->query($checkPost);
    if($result->num_rows>0){
        echo "<script>alert ('Post Already Exist!')</script>";
    }
    else{
        $insertQuery="INSERT INTO src_post(Post,Type)
        VALUES('$postName','$postType')";

        if($conn->query($insertQuery)==True){
            header("location: ad.src.php?Login=success");
            echo "<script>FpostM();
                alert ('Saved');</script>";
        }
        else{
            echo "Error:".$conn->error;
        }
    }
}

if (isset($_POST['editP'])){
    $postName=$_POST['postName'];
    $postType=$_POST['postType'];
    $selector=$_POST['selector'];

    $updateC="UPDATE src_post SET Post='$postName', Type='$postType' WHERE Post_id='$selector'";
    $save=$conn->query($updateC);
    if ($save){
        header("location: ad.src.php?Login=success");
        echo "<script>FpostM();
            alert ('Saved');</script>";
    }
}

if (isset($_POST['deletP'])){
    $selector=$_POST['selector'];
        $deleteC="DELETE FROM src_post WHERE Post_id='$selector'";
        $del=$conn->query($deleteC);
        if ($del){
            header("location: ad.src.php?Login=success");
            echo "<script>FpostM();
                alert ('Deletion Sucessfull');</script>";
        }
    }

if (isset($_POST['addC'])){
    $Cname=$_POST['Cname'];
    $Cindex=$_POST['Cindex'];
    $Creference=$_POST['Creference'];
    $post=$_POST['postC'];
    $image=$_FILES['Image'];
    $image_name=$image['name'];
    $image_type=$image['type'];
    //$image_size=$image['size'];
    $image_tmp=$image['tmp_name'];
    $allowedext=array("image/jpg","image/png","image/jpeg","image/gif","image/PNG");

    $upload_dir="uploads/";
    $image_path=$upload_dir . $image_name;

    $checkPost="SELECT * FROM src_candidate where Full_name='$Cname' or Index_No='$Cindex' or Reference_No='$Creference'";
    $result=$conn->query($checkPost);
    if($result->num_rows>0){
        echo "<script>alert ('Candidate Already Exist!')</script>";
        header("location: ad.src.php?Login=success");
    }
    else{
        if(in_array($image_type,$allowedext)){
            move_uploaded_file($image_tmp,$image_path);
            $insertQuery="INSERT INTO src_candidate(Index_No,Full_Name,Reference_No,Post,Image)
            VALUES('$Cindex','$Cname','$Creference','$post','$image_path')";

            if($conn->query($insertQuery)==True){
                // include 'ad.src.php?Login=success';
                header("location: ad.src.php?Login=success");
                echo "<script>FcandidateM();
                    alert ('Candidate Input Sucessfull');</script>";
            }
            else{
                echo "Error:".$conn->error;
            }
        }
        else{
            //header("location: ad.src.php?Login=success");
            //echo "<script>FcandidateM();</script>";
            echo "<script>alert ('Image Type Invalid!')</script>";
        }
    }
}

if (isset($_POST['editC'])){
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
        $updateC="UPDATE src_candidate SET Index_No='$Cindex',Full_Name='$Cname',Reference_No='$Creference',Post='$post',Image='$image_path' WHERE Index_No='$selector'";
        $save=$conn->query($updateC);
        if ($save){
            header("location: ad.src.php?Login=success");
            echo "<script>FcandidateM();</script>";
            echo "<script>alert ('Saved')</script>";
        }
    }
    else{
        echo "<script>alert ('Image Type Invalid!')</script>";
    }
}

if (isset($_POST['deletC'])){
    $selector=$_POST['selector'];
        $deleteC="DELETE FROM src_candidate WHERE Index_No='$selector'";
        $del=$conn->query($deleteC);
        if ($del){
            header("location: ad.src.php?Login=success");
            echo "<script>FcandidateM();</script>";
            echo "<script>alert ('Deletion Sucessful!')</script>";
        }
        else{
            echo "<script>alert ('There is an error')</script>";
        }
    }

    if(isset($_GET['src_chart'])){
        header("location: src.chart.php");
    }

    if (isset($_POST['addNB'])){
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
            $insertQuery="INSERT INTO src_nb (Message,File)
            VALUES('$msg','$file_path')";

            if($conn->query($insertQuery)==True){
                header("location: ad.src.php?Login=success");
                echo "<script>FNB();</script>";
                echo "<script>alert ('Message Input Sucessfull');</script>";
            }
            else{
                echo "Error:".$conn->error;
            }
    }

    if (isset($_POST['updateNB'])){
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
        $updateC="UPDATE src_nb SET Message='$msg',File='$$file_path' WHERE SN='$selector'";
        $save=$conn->query($updateC);
            if ($save){
                echo "<script>alert ('Saved')</script>";
                //header("location: ad.src.php?Login=success");
            }
    }

    if (isset($_POST['deleteNB'])){
            $selector=$_POST['selector'];
            $delete="DELETE FROM nb WHERE SN='$selector'";
            $del=$conn->query($delete);
            if ($del){
                header("location: ad.src.php?Login=success");
                echo "<script>FNB();
                alert ('Deletion Sucessful!')</script>";
            }
            else{
                echo "<script>alert ('There is an error/nPlease trobleshoot amd try again.')</script>";
            }
        }

    function get_temp_link($stmail,$stun,$url="src.poll.php",$ex=3600){
        session_start();
        $token = bin2hex($stmail);
        $time = time();
        $ex_t = $time + $ex;
        $hash = hash_hmac('sha256',$token . $time, $stun);
        $temp_link = $url.'?token='.$token.'&time='.$time.'&hash='.$hash;
        return $temp_link;
    }
    
    if (isset($_POST['sst'])){

        $table_check="SHOW TABLES LIKE 'src_session'";
        $table=$conn->query($table_check);
        if ($table->num_rows>0){
            $insert="UPDATE session SET session='start' WHERE session='start' OR session='stop'";   

                    $sql="SELECT Student_Email FROM voters";
                    $result=$conn->query($sql);
                    $email=array();
                    while ($row=$result->fetch_assoc()) {
                        array_push($email,$row['Student_Email']);
                    }

                    $From = 'umat-srid';
                    $Subject = 'UMAT SRC';
                    $SuccessMsg = "sent";
                    $FailedMsg = "failed";

                    foreach ($email as $To) {
                    
                        $msg=random_int(1000,9999);
                        $key="SRID.SRC.".$msg;

                        $_SESSION['slink']=get_temp_link($To,$key);
                        $link=substr( $_SESSION['slink'],0,20);

                        $Body = "<p>Click on this link: <a href='$_SESSION[slink]'> SRC Poll </a> to take part in the SRC election.</p>
                        <p>This ' $key '  would be your unique code for the election.</p>
                        <p>Use your student email account and the unique code to login and cast your vote.</p>
                        <p>You would receive an email after done voting to affirm your vote has been cast successfully.</p>";

                        include 'mailer.php';

                        $sql="SELECT * FROM src_sent_links WHERE Student_Email='$To'";
                        $result=$conn->query($sql);
                        if(!$result->num_rows>0){

                            if($sent) {
                                $conn->query($insert);
                                echo "Mail successful!";
                                $insertQuery="INSERT INTO sent_links(Student_Email,Link_Sent)
                                VALUES('$to','$_SESSION[slink]')";
                                $conn->query($insertQuery);
                            }else {
                                echo "Mail failed!";
                            }

                        }
                    }
                    header("location: ad.src.php?Login=success");    
        }else{
            $create_table="CREATE TABLE session(
                session VARCHAR(5) NOT NULL)";
            $conn->query($create_table);
            header("location: ad.src.php?Login=success");
        }
    }

    if (isset($_POST['ssp'])){
        $table_check="SHOW TABLES LIKE 'src_session'";
        $table=$conn->query($table_check);
        if ($table->num_rows>0){
            $insert="UPDATE src_session SET session='stop' WHERE session='start' OR session='stop'";
            $conn->query($insert);
            header("location: ad.src.php?Login=success");
        }else{
            $create_table="CREATE TABLE src_session(
                session VARCHAR(5) NOT NULL)";
            $conn->query($create_table);
        }
    }

    if (isset($_POST['addD'])){
        $dAbb=$_POST['abb'];
        $dName=$_POST['deptN'];
        $dCourse=$_POST['course'];
        $logo=$_FILES['logo'];
        $logo_name=$logo['name'];
        $logo_type=$logo['type'];
        //$logo_size=$logo['size'];
        $logo_tmp=$logo['tmp_name'];
        $allowedext=array("image/jpg","image/png","image/jpeg","image/gif","image/PNG");

        $upload_dir="uploads/";
        $logo_path=$upload_dir . $logo_name;
    
        $checkPost="SELECT * FROM department where Abbreviation='$dAbb'";
        $result=$con->query($checkPost);
        if($result->num_rows>0){
            echo "<script>alert ('Department Already Exist!')</script>";
        }
        else{
            if(in_array($logo_type,$allowedext)){
                move_uploaded_file($logo_tmp,$logo_path);
                $insertQuery="INSERT INTO department(Name,Abbreviation,Course,Logo)
                VALUES('$dName','$dAbb','$dCourse','$logo_path')";
        
                if($con->query($insertQuery)==True){
                    header("location: ad.src.php?Login=success");
                    echo "<script>FpostM();
                        alert ('Saved');</script>";

                    // $sql="CREATE DATABASE $dAbb";
                    $SQL->query("CREATE DATABASE $dAbb");

                    $sql=new mysqli($host,$user,$pass,$dAbb);
                    if ($sql->connect_error){
                        echo "Failed to connect DB".$sql->connect_error;
                    }

                    $create_Candidate="CREATE TABLE Candidate(
                        Index_No VARCHAR(17) NOT NULL,
                        Full_Name VARCHAR(50) NOT NULL,
                        Reference_No INT(10) NOT NULL,
                        Post TEXT NOT NULL,
                        Image VARCHAR(225) NOT NULL)";
                    $sql->query($create_Candidate);

                    $create_nb="CREATE TABLE nb(
                        SN INT AUTO_INCREMENT PRIMARY KEY,
                        Message VARCHAR(500) NOT NULL,
                        File VARCHAR(225) NOT NULL)";
                    $sql->query($create_nb);

                    $create_Post="CREATE TABLE post(
                        Post_id INT AUTO_INCREMENT PRIMARY KEY,
                        Post VARCHAR(100) NOT NULL,
                        Type VARCHAR(100) NOT NULL)";
                    $sql->query($create_Post);

                    $create_Voters="CREATE TABLE voters(
                        Index_No VARCHAR(17) NOT NULL,
                        Last_Name VARCHAR(50) NOT NULL,
                        Other_Name VARCHAR(50) NOT NULL,
                        Voters_Id VARCHAR(10) NOT NULL,
                        Email VARCHAR(100) NOT NULL,)";
                    $sql->query($create_Voters);

                    $create_Votes="CREATE TABLE votes(
                        Index_No VARCHAR(17) NOT NULL,
                        Name TEXT NOT NULL)";
                    $sql->query($create_Votes);

                    $create_feedback="CREATE TABLE feedback(
                        Feedback TEXT NOT NULL";
                    $sql->query($create_feedback);

                    $create_ec_statement="CREATE TABLE ec_statement(
                        Title TEXT NOT NULL,
                        Statement TEXT NOT NULL)";
                    $sql->query($create_ec_statement);

                    $create_session="CREATE TABLE session(
                        session VARCHAR(5) NOT NULL)";
                    $sql->query($create_session);

                    $insertQuery="INSERT INTO session(session)
                    VALUES('stop')";
                    $sql->query($insertQuery);
                }
                else{
                    echo "Error:".$conn->error;
                }
            }else{
                echo "<script>alert ('Logo Type Invalid!')</script>";
            }
        }
    }

    if (isset($_POST['deleteD'])){
        $sn=$_POST['selector'];
        $deleteC="DELETE FROM department WHERE Abbreviation='$sn'";
        $del=$con->query($deleteC);
        $SQL->query("DROP DATABASE $sn");
        header("location: ad.src.php?Login=success");
    }

    // if (isset($_POST['submit_feed'])){
    //     $feedback=$_POST['feedback'];
        
    //     $insertQuery="INSERT INTO feedback(Feedback)
    //         VALUES('$feedback')";
    
    //     if($conn->query($insertQuery)==True){
    //         header("location: vhome.php?Login=success");
    //         echo "<script>alert ('Submited');</script>";
    //     }
    //     else{
    //         echo "Error:".$conn->error;
    //     }
    // }

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