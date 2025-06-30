<?php
include("resources.php");
include("connect.php");

$d=$dom."_session";
$get_s="SELECT * FROM $d";
$session=$conn->query($get_s);
$ses=$session->fetch_assoc();
if ($ses['release']== 1){
    $link = $Domain."result.$dom.php";
    $class = '#';
    $info = "<h5 style='color: black;'>Result is releaded !!!</h5>";
}else {
    $link = "#";
    $class = 'disabled';
    $info = "<h5 style='color: black;'>Result is not yet releaded !!!</h5>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href=<?php echo ($Domain."style.css");?>>
    <title>UMAT-SRID VOTING SYSTEM</title>
    <style>
        a{
            background: rgba(15, 146, 221, 0.99);
            border:none;
            z-index:1;
            border-radius:10px;
            color:white;
            width: 70%;
            padding:5px;
            margin: 20px;
            cursor: pointer;
        }
        a:hover{
            background: rgb(6, 106, 163);
        }
        .disabled{
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="pv">
            <div id="waterm"></div>
                <h1 style="color:green;">⏳</h1>
                <h1>Link Expired!</h1>
                <a href=<?php echo ($link);?> class=<?php echo ($class);?>>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big w-16 h-16 text-green-500 mx-auto mb-4" aria-hidden="true">
                <path d="m9 12 2 2 4-4"></path>
                <path d="M5 7c0-1.1.9-2 2-2h10a2 2 0 0 1 2 2v12H5V7Z"></path>
                <path d="M22 19H2"></path></svg>
                <h3 width="24" height="24">View Results</h3>
            </a>
            <?php echo $info; ?>
            </div>
        </div>
    </div>
</body>
</html>