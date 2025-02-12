<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <title>UMAT VOTING RESULT </title>
</head>
<body>
    <script src="canvasjs.min.js"></script>
    <div class="wrapper" style="flex-direction: column;">
        <div class="content" style="height: 100%;">
            <div id="h" style="margin:1%;">
                <img src="images/u9.PNG">
            </div>
            <h1>SRC RESULT CHART</h1>
            <?php
            function sort_resut($VP){
                include 'connect.php';
                $table_check="SHOW TABLES LIKE '$VP'";
                $table=$jconn->query($table_check);
                if ($table->num_rows>0){
                    $getcandidates="SELECT * FROM candidate WHERE Post='$VP'";
                    $result=$jconn->query($getcandidates);
                    $getPost="SELECT * FROM post WHERE Post='$VP'";
                    $format=$jconn->query($getPost);
                    $row=$format->fetch_assoc();
                    $n=1;
                    $N=2;
                    $v =$VP . "p";
                    //echo"<br><h2 align='center'>$VP</h2><br>";

                    if ("Multi-Voting"==$row['Type']){
                        $dataPoints = array();
                        foreach($result as $Pin){
                            $getresult="SELECT * FROM $VP WHERE Candidate='$Pin[Full_Name]'";
                            $poll=$jconn->query($getresult);
                            $pollC=$poll->num_rows;

                            $temp_array = array("y" => $pollC, "label" => $Pin['Full_Name'] );
                            array_push($dataPoints, $temp_array);
                        }
                        ?>
                        <div id="<?php echo $v; ?>" style="align-self: center; margin: 5%; height: 370px; width: 85%;"></div>
                            <script>
                                function chart(){
                                var d="<?php echo $v; ?>";
                                var chart = new CanvasJS.Chart(d, {
                                    animationEnabled: true,
                                    theme: "light",
                                    title:{
                                        text: "<?php echo $VP; ?>"
                                    },
                                    axisY: {
                                        title: "Votes"
                                    },
                                    data: [{
                                        type: "column",
                                        yValueFormatString: "#,##0.## votes",
                                        dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                                    }]
                                });
                                chart.render();
                                
                                }
                                chart();
                            </script>
                        <?php
                    }
                    elseif ("Referendum"==$row['Type']) {
                        $dataPoints1 = array();
                        $dataPoints2 = array();
                        foreach($result as $Pin){
                            $getresult="SELECT * FROM $VP WHERE Candidate='$Pin[Full_Name]' AND Votes='1'";
                            $poll=$jconn->query($getresult);
                            $pollC=$poll->num_rows;
                            $getresult2="SELECT * FROM $VP WHERE Candidate='$Pin[Full_Name]' AND Votes='2'";
                            $poll2=$jconn->query($getresult2);
                            $pollC2=$poll2->num_rows;

                            $dataPointsYes = array("y" => $pollC, "label" => $Pin['Full_Name'] );

                            $dataPointsNo = array("y" => $pollC2, "label" => $Pin['Full_Name'] );

                            array_push($dataPoints1, $dataPointsYes);
                            array_push($dataPoints2, $dataPointsNo);
                        }
                        ?>
                        <div id="<?php echo $VP; ?>" style="align-self: center; margin: 5%; height: 370px; width: 85%;"></div>
                            <script>
                                function chart(){
                                    var chartContainer="<?php echo $VP; ?>";
                                    var chart = new CanvasJS.Chart(chartContainer, {
                                        animationEnabled: true,
                                        theme: "light",
                                        title:{
                                            text: "<?php echo $VP; ?>"
                                        },
                                        axisY:{
                                            includeZero: true
                                        },
                                        legend:{
                                            cursor: "pointer",
                                            verticalAlign: "center",
                                            horizontalAlign: "right",
                                            itemclick: toggleDataSeries
                                        },
                                        data: [{
                                            type: "column",
                                            name: "Yes",
                                            indexLabel: "{y}",
                                            yValueFormatString: "#0.## votes",
                                            showInLegend: true,
                                            dataPoints: <?php echo json_encode($dataPoints1, JSON_NUMERIC_CHECK); ?>
                                        },{
                                            type: "column",
                                            name: "No",
                                            indexLabel: "{y}",
                                            yValueFormatString: "#0.## votes",
                                            showInLegend: true,
                                            dataPoints: <?php echo json_encode($dataPoints2, JSON_NUMERIC_CHECK); ?>
                                        }]
                                    });
                                    chart.render();
                                    
                                    function toggleDataSeries(e){
                                        if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                                            e.dataSeries.visible = false;
                                        }
                                        else{
                                            e.dataSeries.visible = true;
                                        }
                                        chart.render();
                                    }
                                    
                                    }
                                    chart();
                            </script>
                        <?php
                        }
                    }
                }

                include("jfunc.php");
                $posts=get_post();
                foreach($posts as $post){
                    sort_resut($post['POST']);   
                }
            ?>
        </div>
    </div>
</body>
</html>
