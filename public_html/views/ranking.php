<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>رتبه&zwnj;بندی شاززز</title>
    <link rel="icon" href="//shaazzz.ir/logo.png">
    <link href="//training.shaazzz.ir/files/styles.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v26.0.2/dist/font-face.css" rel="stylesheet"
          type="text/css"/>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>


<body>
<noscript>Sorry, your browser does not support JavaScript!</noscript>

<div class="container">
    <br>
    <div id="contact">

        <img style="align-content:center;padding-left:20%;padding-right:20%;width: 100%;min-height:25%;margin:auto;vertical-align:middle;display:inline;"
             src="//training.shaazzz.ir/logo.png">
        <br><br>
        <h3 dir="rtl" style="font-size:45px;text-align:center">رتبه&zwnj;بندی شاززز</h3>

        <?php
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        try {
            AllUsers::readFromFile();
            $today = (int)file_get_contents("data/counter.txt");
            $number = 0;
            echo "<table dir='rtl' style='margin: 15px;'><tr><th style='font-size:20px;padding: 20px'>رتبه</th><th style='font-size:20px;padding: 20px'>نام</th><th style='font-size:20px;padding: 20px'>امتیاز</th></tr>";

            $cfApi = new CodeforcesApi();
            usort(AllUsers::$users, function ($a, $b) {
                if ($b->warm > $a->warm) {
                    return 1;
                } else {
                    return -1;
                }
            });

            $usernames = array();
            foreach (AllUsers::$users as $user) {
                array_push($usernames, $user->username);
            }

            $usersInf = $cfApi->request("user.info", array("handles" => implode(';', $usernames)))['result'];

            foreach (AllUsers::$users as $user) {
                $number++;
                $fullName = $user->username;
                if (isset($usersInf[$number - 1]["firstName"]) && isset($usersInf[$number - 1]["lastName"])) {
                    $fullName = $usersInf[$number - 1]["firstName"] . " " . $usersInf[$number - 1]['lastName'];
                }
                $warm = (int)$user->warm;
                $photo = $usersInf[$number - 1]['avatar'];
                $linkName = str_replace(".", "+", $user->username);

                $userRateColor = $user->getRate()['labelColor'];
                $fontSize="20px";
                if(strlen($fullName)>25){
                    $fontSize="15px";
                }
                echo "<tr id=\"user\" style='margin: 100px'>
                        <td style='text-align:center;padding: 5px'>#$number</td>
                        <td dir='auto' style='font-size: 20px;width: 100%;padding: 15px'>
                            <img class=\"circular--square\" style=\"width: 10%;min-height:10%;vertical-align:middle;display:inline;\" src=\"$photo\">
                            <a href=\"profile/$linkName/\" style='font-size:$fontSize;color: $userRateColor;text-decoration: none;margin-left: 3px;margin-right: : 3px'>$fullName</a>
                        </td>
                        <td dir='rtl' style='text-align:center;padding: 5px'>$warm</td>
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
