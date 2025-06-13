<?php
$host="localhost";
$user="root";
$pass="JK";
$dbs="umat_src_poll";

$conn=new mysqli($host,$user,$pass,$dbs);
if ($conn->connect_error){
    echo "Failed to connect DB".$conn->connect_error;
}

?>