<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>فرم گزارش کاربران</title>
    <link href="styles.css" rel="stylesheet" type="text/css">
</head>


<body>
<noscript>Sorry, your browser does not support JavaScript!</noscript>

<div class="container">

    <?php
    $inputs = array("username");

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    try {
        chdir('..');
        require __DIR__ . '/../data/defines.php';
        require __DIR__ . '/../Models/problemset.php';
        require __DIR__ . '/../Models/CodeforcesUserApi.php';

        foreach ($inputs as $input) {
            if (!isset($_POST[$input])) {
                throw new Exception("_POST input error");
            }
        }
        $setting = json_decode(file_get_contents("data/rankingSettings"), true);
        $username = (int)$_POST["username"];

        $api = new CodeforcesUserApi();
        $api->login(CODEFORCES_USERNAME, CODEFORCES_PASSWORD);
        $cfApi = new CodeforcesApi();
        AllContests::readFromFile();
        $userScore = array();

    } catch (Exception $e) {
        if ($e->getMessage() != "_POST input error") {
            echo sprintf("<errorbox><h4 dir=\"rtl\"> <b>خطا:</b> %s</h4></errorbox><br>", $e->getMessage());
        }
    }
    ?>

</div>
</body>
</html>

