<?php
    function no_of_voters(){
        include 'connect.php';
        $getVoters="SELECT * FROM voters";
        $result=$conn->query($getVoters);
        $Voters=$result->num_rows;
        return $Voters;
    }
    $voters = no_of_voters();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <title>Document</title>
</head>
<body>
    <div class="results-grid" style="margin:auto; width:80%">
        <div class="results-list">
            <h3>Sending Links to All Voters</h3>
            <div class="candidate-result">
                <div class="candidate-info">
                    <div class="candidate-name" id="name" style="color:white;">
                        Number of voters: <?php echo $voters; ?>
                    </div>
                    <div class="vote-bar">
                        <div class="vote-fill" id="fill" style="width: <?php echo $per;?>%"></div>
                    </div>
                    <div class="vote-stats">
                        <span id="links_sent">links sent:</span>
                        <span id="links_per">voters sent%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>