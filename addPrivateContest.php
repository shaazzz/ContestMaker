</<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Document</title>
</head>
<body>
<h3 dir="rtl">اگر میخواهید با اکانت پیشفرض سوال ها اضافه شوند
    username و password
    را خالی بگذارید</h3><br>
<form action="addPrivateContest.php" method="post">
    <input style="width: 20em;" name="username" value="" placeholder="username"><br>
    <input style="width: 20em;" name="password" value="" type="password" placeholder="password"><br>
    <input style="width: 20em;" name="contestAddressPrefix" value="gym" placeholder="contest address prefix"><br>
    <input style="width: 20em;" name="contestId" value="" placeholder="contest id"><br>
    <input style="width: 20em;" name="fromProblemNumber" value="0" placeholder="from problem number 0-base"><br>
    <input style="width: 20em;" name="problemCount" value="1" placeholder="problem count"><br>
    <input style="width: 20em;" name="prior" value="" placeholder="prior"><br>
    <input style="width: 20em;" name="defaultDifficulty" value="2400" placeholder="default difficulty"><br>
    <input class="submit" type="submit" value="Submit">
</form>
</body>
</html>

<?php
$inputs = array("username", "password", "contestAddressPrefix", "contestId", "defaultDifficulty", "fromProblemNumber", "problemCount", "prior");

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/Models/problemset.php';
require __DIR__ . '/data/defines.php';
require __DIR__ . '/CodeforcesUserApi.php';

foreach ($inputs as $input) {
    if (!isset($_POST[$input])) {
        die($input);
    }
}
$api = new CodeforcesUserApi();
if (strlen($_POST["username"]) > 0) {
    $api->login($_POST["username"], $_POST["password"]);
} else {
    $api->login(CODEFORCES_USERNAME, CODEFORCES_PASSWORD);
}
$L = (int)$_POST["fromProblemNumber"];
$R = $L + (int)$_POST["problemCount"];
$problemQueries = $api->getContestProblemQueries($_POST["contestId"]);
problemset::readFromFile();
for ($i = $L; $i < $R; $i++) {
    if (isset($problemQueries[$i])) {
        $data = $api->getProblemArrayData($problemQueries[$i]);
        if (!isset($data['rating'])) {
            $data['rating'] = $_POST['defaultDifficulty'];
        }
        echo $data['tags'];
        problemset::addProblem($data['id'], $problemQueries[$i], json_decode($data['tags'], true), (int)$data['rating'], (float)$_POST["prior"], false);
    }
}


?>