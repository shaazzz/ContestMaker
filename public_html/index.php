<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>رتبه بندی کل</title>
    <link href="styles.css" rel="stylesheet" type="text/css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>


<body>
<noscript>Sorry, your browser does not support JavaScript!</noscript>

<div class="container">
    <br>
    <div id="contact">

        <img style="align-content:center;padding-left:20%;padding-right:20%;width: 100%;min-height:25%;margin:auto;vertical-align:middle;display:inline;" src="http://bayanbox.ir/view/4819107262267230957/rlogo.png">
        <br><br>
        <h3 dir="rtl" style="font-size:45px;text-align:center">رتبه بندی کل</h3>

        <?php
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        try {
            chdir('..');
            require __DIR__ . '/../data/defines.php';
            require __DIR__ . '/../Models/problemset.php';
            require __DIR__ . '/../Models/AllUsers.php';
            require __DIR__ . '/../Models/CodeforcesUserApi.php';
            AllUsers::readFromFile();
            $today = (int)file_get_contents("data/counter.txt");
            $number = 0;
            echo "<table dir='rtl' style='margin: 15px;'><tr><th style='font-size:20px;padding: 20px'>رتبه</th><th style='font-size:20px;padding: 20px'>نام</th><th style='font-size:20px;padding: 20px'>امتیاز</th></tr>";

            usort(AllUsers::$users, function ($a, $b) {
                return $b->warm - $a->warm;
            });

            foreach (AllUsers::$users as $user) {
                $number++;
                $name = $user->fullName;
                $warm = (int)$user->warm;
                echo "<tr id=\"user\" style='margin: 100px'>
                        <td style='text-align:center;padding: 10px'>#$number</td>
                        <td dir='auto' style='font-size: 20px;width: 100%;padding: 20px'>
                            <a href=\"/report.php?username=$user->username\" class=\"fill-div\">$name</a>
                        </td>
                        <td dir='rtl' style='text-align:center;padding: 10px'>$warm</td>
                        </tr>";
            }
            echo "</table>";
        } catch (Exception $e) {
            if ($e->getMessage() != "_POST input error") {
                echo "<errorbox><h4 dir=\"rtl\"> <b>خطا:</b> " . $e->getMessage() . "</h4></errorbox><br>";
            }
        }
        ?>
    </div>

</div>
</body>
</html>
