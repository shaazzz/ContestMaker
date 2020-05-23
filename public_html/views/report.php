<?php
$username = $_GET['input'];
AllUsers::readFromFile();
if(!isset(AllUsers::$users[$username])){
    require_once '404.php';
    return;
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>امتیاز کاربر</title>
    <link href="//training.shaazzz.ir/styles.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="http://bayanbox.ir/view/4819107262267230957/rlogo.png">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v26.0.2/dist/font-face.css" rel="stylesheet" type="text/css" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            var chart = new CanvasJS.Chart("chartContainer", {
                title: {
                    text: "Shaazzz Rating"
                },
                exportFileName: "Shaazzz Rating",
                theme: "light2",
                axisY: {
                    thickness: 0,
                    stripLines: <?php
                    echo file_get_contents("data/rateColors.txt");
                    ?>,
                    valueFormatString: "####",
                    gridThickness: 0
                },
                axisX: {
                    valueFormatString: "#",
                    interval: 7,
                    prefix: "day "
                },
                data: [
                    {
                        type: "line",
                        color: "#040568",
                        dataPoints: <?php
                        ini_set('display_errors', 1);
                        error_reporting(E_ALL);
                        $inputs = array("input");
                        try {
                            foreach ($inputs as $input) {
                                if (!isset($_GET[$input])) {
                                    throw new Exception("_GET input error");
                                }
                            }
                            $username = $_GET['input'];
                            $today = (int)file_get_contents("data/counter.txt");
                            echo json_encode(AllUsers::$users[$username]->getRating($today + 1));
                        } catch (Exception $e) {
                            if ($e->getMessage() != "_POST input error") {
                                echo sprintf("<errorbox><h4 dir=\"rtl\"> <b>خطا:</b> %s</h4></errorbox><br>", $e->getMessage());
                            }
                        }
                        ?>
                    }
                ]
            });
            chart.render();
        }
    </script>
    <script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</head>


<body>

<div class="container">
    <noscript>Sorry, your browser does not support JavaScript!</noscript>
    <div id="contact" style="min-height:20%;">
        <?php

        $cfApi = new CodeforcesApi();
        $user = $cfApi->request("user.info", array("handles" => $_GET['input']))['result'][0];
        //var_dump($user);
        $rates = json_decode(file_get_contents("data/rateColors.txt"), true);
        $userRateName = 0;
        $userRateColor = null;
        foreach ($rates as $rate) {
            if (AllUsers::$users[$username]->warm < $rate['endValue']) {
                $userRateName = $rate['name'];
                $userRateColor = $rate['color'];
                break;
            }
        }

        $fullName = $_GET['input'];
        if (isset($user["firstName"]) && isset($user["lastName"])) {
            $fullName = $user["firstName"] . " " . $user['lastName'];
        }

        echo "<img class='circular-big-square' style=\"width: 40%;min-height:25%;\" src=\"" . $user['titlePhoto'] . "\">";
        echo "<a href=\"https://codeforces.com/profile/$username\">
<img border=\"0\" alt=\"W3Schools\" src=\"https://sta.codeforces.com/s/22391/apple-icon-57x57.png\" width=\"50\" height=\"50\">
</a>";
        echo "<div dir='rtl' style=\"font-size: 20px;color:" . $userRateColor . ";\">";
        echo "<h4 style=\"margin-top:20px;font-size: 35px;text-align:center;\">$fullName</h4>";
        echo "<h4 style=\"font-size: 20px;text-align:center\"><b>$userRateName</b></h4>";
        echo "</div>";
        echo "<h4 dir='rtl' style=\"text-align:center;font-size: 20px;\"> امتیاز: " . (int)AllUsers::$users[$username]->warm . "</h4>";
        ?>
        <div id="chartContainer" style="margin-top: 30px; height: 300px; width: 100%;"></div>
    </div>
</div>
</body>
</html>
