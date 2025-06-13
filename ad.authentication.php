<?php
include 'connect.php';

if (isset($_POST['submit'])){
    $l_name=$_POST['l_name'];
    $m_name=$_POST['m_name'];
    $f_name=$_POST['f_name'];
    $tel=$_POST['tel'];
    $status=$_POST['status'];
    $email=$_POST['mail'];
    $management=$_POST['management'];
    $image=$_FILES['image'];
    $image_name=$image['name'];
    $image_type=$image['type'];
    //$image_size=$image['size'];
    $image_tmp=$image['tmp_name'];
    $allowedext=array("image/jpg","image/png","image/jpeg","image/gif","image/PNG");

    $upload_dir="uploads/";
    $image_path=$upload_dir . $image_name;

    function id(){
        include 'connect.php';
        $i=random_int(1,10000);
        $s_no = "ad.srid.".$i;
        $checkid="SELECT * FROM admin where Unique_No='$s_no'";
        $result=$conn->query($checkid);
        while ($result->num_rows>0){
            id();
        }
        $id="ad.srid.".$i;
        return $id;
    }
    $Unique_No=id();

    if(empty($l_name)||empty($m_name)||empty($f_name)||strlen($tel)!=10||empty($status)||empty($email)||empty($management)){
        echo<<<EOT
        <script>
        alert("Make sure all field are filled and with valid info!")
        </script>
        EOT;
    }else{
        if(in_array($image_type,$allowedext)){
            move_uploaded_file($image_tmp,$image_path);
            $insertQuery="INSERT INTO admin(Unique_No,Last_Name,First_Name,Middle_Name,Status,Email,Tel,Management,Image)
            VALUES('$Unique_No','$l_name','$f_name','$m_name','$status','$email','$tel','$management','$image_path')";
        }else{
            //header("location: ad.src.php?Login=success");
            //echo "<script>FcandidateM();</script>";
            echo "<script>alert ('Image Type Invalid!')</script>";
        }

        if($conn->query($insertQuery)==True){
            echo "<script>alert('Unique Number is $Unique_No')</script>";
            header("location: ad.reg.php");

        }
        else{
            echo "Error:".$conn->error;
        }
    }
}

if (isset($_POST['ad_delete'])){
    $selector=$_POST['select'];
        $deleteC="DELETE FROM admin WHERE Unique_No='$selector'";
        $del=$conn->query($deleteC);
        if ($del){
            header("location: ad.reg.php");
            echo "<script>alert ('Deletion Sucessfull');</script>";
        }
    }

if (isset ($_POST['login'])){
    $mail=$_POST['mail'];
    $unique=$_POST['un'];
    $sql="SELECT * FROM admin WHERE Unique_No='$unique' and Email='$mail'";
    $result=$conn->query($sql);
        if($result->num_rows>0){
            session_start();
            $row=$result->fetch_assoc();
            $_SESSION['Unique_No']=$row['Unique_No'];
            $_SESSION['Last_Name']=$row['Last_Name'];
            $_SESSION['First_Name']=$row['First_Name'];
            $_SESSION['Middle_Name']=$row['Middle_Name'];
            $_SESSION['Status']=$row['Status'];
            $_SESSION['Email']=$row['Email'];
            $_SESSION['Tel']=$row['Tel'];
            $_SESSION['Management']=$row['Management'];
            $_SESSION['Image']=$row['Image'];
            if(empty($_SESSION['Image'])){
                $_SESSION['Image']="images/c1.jpeg";
            }
            switch ($_SESSION['Management']) {
                case 'SRC':
                    header("location: ad.src.php?Login=success");
                    break;
                case 'JCR':
                    header("location: ad.jcr.php?Login=success");
                    break;
                
                default:
                    header("location: ad.src.php?Login=success");
                    break;
            }
        }
        else{
            echo "Not Found, Incorrect Voter's Id or Password";
        }
}
?>