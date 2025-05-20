<?php
$host="localhost";
$user="root";
$pass="JK";
// $db="umat_accounts";
$dbs="umat_src_poll";
// $dbj="umat_jcr_poll";
//$dbd="umat_dept_poll";

// $SQL=new mysqli($host,$user,$pass);
// if ($SQL->connect_error){
//     echo "Failed to connect DB".$SQL->connect_error;
// }

// $con=new mysqli($host,$user,$pass,$db);
// if ($con->connect_error){
//     echo "Failed to connect DB".$con->connect_error;
// }

$conn=new mysqli($host,$user,$pass,$dbs);
if ($conn->connect_error){
    echo "Failed to connect DB".$conn->connect_error;
}

// $jconn=new mysqli($host,$user,$pass,$dbj);
// if ($jconn->connect_error){
//     echo "Failed to connect DB".$jconn->connect_error;
// }
?>