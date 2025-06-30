<?php
include 'connect.php';
$get_s="SELECT * FROM jcr_session";
$session=$conn->query($get_s);
$ses=$session->fetch_assoc();
if ($ses['release']== 0){
    header("location: jcr.poll.php");
}

function get_post(){
    include 'connect.php';
    $getPost="SELECT POST FROM jcr_post";
    $format=$conn->query($getPost);
    $row=$format->fetch_assoc();
    foreach($format as $Pin){
        $list[]=$Pin;   
    }
    return $list;
}

function no_of_votes(){
    include 'connect.php';
    $getVotes="SELECT * FROM jcr_votes";
    $result=$conn->query($getVotes);
    $No_of_Votes=$result->num_rows;
    return $No_of_Votes;
}

  function result($VP){
        include 'connect.php';
        $table_check="SHOW TABLES LIKE '$VP'";
        $table=$conn->query($table_check);
        $Query="SELECT * FROM jcr_result WHERE Position='$VP'";
        $data = $conn->query($Query);
        $get = $data->fetch_assoc();

        if ($table->num_rows>0){

            if ("Multi-Voting"==$get['Type']){
                $return = [];
                $results = json_decode($get['Results']);
                $v= str_replace("_"," ",substr($VP,4));
                foreach($results as $Pin){
                    $per = (no_of_votes() != 0) ? number_format(($Pin->results / no_of_votes()) * 100, 2) : 0;
                    $return[] = [$v,$Pin->image,$Pin->name,$Pin->results,$per];
                }
                if ($return !== null){
                    usort($return, function($a,$b) {
                        return $b[3] <=> $a[3];
                    });
                    return $return;
                }
                return null;
            }
            elseif ("Referendum"==$get['Type']) {
                $return = [];
                $results = json_decode($get['Results']);
                $v= str_replace("_"," ",substr($VP,4));
                foreach($results as $Pin){
                    $pery = (no_of_votes() != 0) ? number_format(($Pin->results_y / no_of_votes()) * 100, 2) : 0;
                    $pern = (no_of_votes() != 0) ? number_format(($Pin->results_n / no_of_votes()) * 100, 2) : 0;
                    $return[] = ([$v,$Pin->image,$Pin->name,"Yes: $Pin->results_y</h4>","No: $Pin->results_n</h4>",$pery,$pern]);      
                }
                if ($return !== null){
                    usort($return, function($a,$b) {
                        return $b[3] <=> $a[3];
                    });
                    return $return;
                }
                return null;
            }
        }
    }

    $totalVotes = no_of_votes();
    $posts = get_post();

    echo"
    <script>
        var post = [];
        var postID = [];
    </script>";
foreach($posts as $post){
    $a = $post['POST'];
    // $postButtonID[] = $a;
    $b = 's'.$post['POST'];
    $postID[] = $b;
    echo"
    <script>
        post.push('$post[POST]');
        postID.push('$b');
    </script>";
}
    // print_r($postID);
    include("resources.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href=<?php echo ($Domain."result.css");?>>
    <title>Voting Results</title>
</head>
<body>
    <div id="h">
        <h1></h1>
        <div id="hb">
            <?php
                $i= 0;
                foreach ($posts as $post){
                    $p = str_replace("_"," ",substr($post['POST'],4));
                    echo <<<EOT
                        <button id="{$postID[$i]}">$p</button>
                    EOT;
                    $i++;
                }
            ?>
        </div>
    </div>
    <?php
        $i = 0;
        foreach ($posts as $post) :
            $results = result($post['POST']);
            if ($i == 0){
                $display="block";
            }else{
                $display="none";
            }
            $i++;
            ?>
        <div class="container" id="<?php echo $post['POST']; ?>" style = "display:<?php echo $display; ?>;">
            <!-- Header -->
            <div class="header">
                <h1>🗳️ Election Results Dashboard</h1>
                <p>Real-time voting results and analytics</p>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($totalVotes); ?></div>
                    <div class="stat-label">Total Votes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $results!==null? count($results) : null ; ?></div>
                    <div class="stat-label">Candidates</div>
                </div>
            </div>

            <!-- Results Grid -->
            <div class="results-grid">
                <!-- Candidates Results -->
                <div class="results-list">
                    <h3>Detailed Results</h3>
                    <?php if ($results !== null){
                        foreach ($results as $index => $candidate): ?>
                            <h3><?php echo ($candidate[0]); ?></h3>
                            <?php if (count($candidate) == 7){ ?>
                                <div class="candidate-result <?php echo $candidate[3]>$candidate[4] ? 'winner' : ''; ?>">
                                    <img class="candidate-avatar" src = <?php echo ($candidate[1]); ?>>
                                    <div class="candidate-info">
                                        <div class="candidate-name">
                                            <?php echo htmlspecialchars($candidate[2]); ?>
                                            <?php if ($candidate[3]>$candidate[4]): ?>
                                                <span style="color: #f39c12;">👑</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="vote-bar">
                                            <div class="vote-fill" style="width: <?php echo $candidate[5]; ?>%"></div>
                                        </div>
                                        <div class="vote-stats">
                                            <span><?php echo number_format($candidate[3]); ?> votes</span>
                                            <span><?php echo $candidate[5]; ?>%</span>
                                        </div>
                                        <div class="vote-bar">
                                            <div class="vote-fill" style="width: <?php echo $candidate[6]; ?>%"></div>
                                        </div>
                                        <div class="vote-stats">
                                            <span><?php echo number_format($candidate[4]); ?> votes</span>
                                            <span><?php echo $candidate[6]; ?>%</span>
                                        </div>
                                    </div>
                                </div>
                            <?php }else{ ?>
                                <div class="candidate-result <?php echo $index === 0 ? 'winner' : ''; ?>">
                                    <img class="candidate-avatar" src = <?php echo ($candidate[1]); ?>>
                                    <div class="candidate-info">
                                        <div class="candidate-name">
                                            <?php echo htmlspecialchars($candidate[2]); ?>
                                            <?php if ($index === 0): ?>
                                                <span style="color: #f39c12;">👑</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="vote-bar">
                                            <div class="vote-fill" style="width: <?php echo $candidate[4]; ?>%"></div>
                                        </div>
                                        <div class="vote-stats">
                                            <span><?php echo number_format($candidate[3]); ?> votes</span>
                                            <span><?php echo $candidate[4]; ?>%</span>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php endforeach;
                    }?>
                </div>

            </div>

        </div>
    <?php endforeach; ?>
    <script src=<?php echo ($Domain."result.js");?>></script>
</body>
</html>