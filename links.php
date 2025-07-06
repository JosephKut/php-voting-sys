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
                        <div class="vote-fill" id="fill" style="width: 0%"></div>
                    </div>
                    <div class="vote-stats">
                        <span id="links_sent">links sent:</span>
                        <span id="links_per">voters sent%</span>
                    </div>
                </div>
            </div>
            <button onclick="window.history.back();" style="margin-top:15px; width: 100px; margin-left: 80%;">Back</button>
        </div>
    </div>
</body>
</html>