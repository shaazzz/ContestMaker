<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>فرم گزارش کاربران</title>
    <link href="styles.css" rel="stylesheet" type="text/css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <link href="styles.css" rel="stylesheet" type="text/css">
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
                    stripLines: [
                        {
                            startValue: 0,
                            endValue: 10,
                            color: "rgb(192,192,192)"
                        },{
                            startValue: 10,
                            endValue: 20,
                            color: "rgb(36,186,9)"
                        },{
                            startValue: 20,
                            endValue: 30,
                            color: "#1e51d2"
                        },{
                            startValue: 30,
                            endValue: 45,
                            color: "#ce1760"
                        },{
                            startValue: 45,
                            endValue: 55,
                            color: "#dcd130"
                        },{
                            startValue: 55,
                            endValue: 70,
                            color: "#e21d1d"
                        },{
                            startValue: 70,
                            endValue: 100,
                            color: "#9e0505"
                        },
                    ],
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

                        $inputs = array("username");

                        try {
                            chdir('..');
                            require __DIR__ . '/../data/defines.php';
                            require __DIR__ . '/../Models/problemset.php';
                            require __DIR__ . '/../Models/CodeforcesUserApi.php';

                            foreach ($inputs as $input) {
                                if (!isset($_GET[$input])) {
                                    throw new Exception("_GET input error");
                                }
                            }
                            $username = $_GET["username"];
                            AllUsers::readFromFile();
                            echo json_encode(AllUsers::$users[$username]->getRating(int(file_get_contents("/../data/counter.txt"))));
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
    <div id="chartContainer" style="height: 300px; width: 100%;"></div>
</div>
</body>
</html>
