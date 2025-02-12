<?php
include 'connect.php';
$sql="SELECT Student_Email FROM voters";
$result=$con->query($sql);
$email=array();
while ($row=$result->fetch_assoc()) {
    # code...
    array_push($email,$row['Student_Email']);
}
print_r ($email[2]);
?>