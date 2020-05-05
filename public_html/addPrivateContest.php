<?php
$inputs = array("username", "password", "contestAddressPrefix", "contestId", "defaultDifficulty", "fromProblem", "toProblem", "prior");

ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    chdir('..');
    require __DIR__ . '/../data/defines.php';
    require __DIR__ . '/../Models/problemset.php';
    require __DIR__ . '/../Models/CodeforcesUserApi.php';

    foreach ($inputs as $input) {
        if (!isset($_POST[$input])) {
            throw new Exception("$input وارد نشده است ");
        }
    }
    if (!is_numeric($_POST["prior"]) || (float)$_POST["prior"] < 0 || (float)$_POST["prior"] > 0.5) {
        throw new Exception("prior باید بین ۰ و ۰.۵ باشد!");
    }
    $api = new CodeforcesUserApi();
    if (strlen($_POST["username"]) > 0) {
        $api->login($_POST["username"], $_POST["password"]);
    } else {
        $api->login(CODEFORCES_USERNAME, CODEFORCES_PASSWORD);
    }
    $L = ord($_POST["fromProblem"]) - ord('A');
    $R = ord($_POST["toProblem"]) - ord('A');
    if ($L < 0 || $L >= 26) {
        throw new Exception("(from problem index) به درستی وارد نشده است!");
    }
    if ($R < $L || $R >= 26) {
        throw new Exception("(to problem index) به درستی وارد نشده است!");
    }
    $problemQueries = $api->getContestProblemQueries($_POST["contestId"], $_POST["contestAddressPrefix"]);
    problemset::readFromFile();
    $allTags = json_decode(file_get_contents("data/allTags.txt"), true);
    $additionalTags = array();
    if (strlen($_POST['additionalTags'] > 0)) {
        $additionalTags = explode(',', strtolower($_POST['additionalTags']));
        foreach ($additionalTags as $tag) {
            if (!in_array($tag, $allTags)) {
                throw new Exception("تگ $tag  به درستی وارد نشده است!");
            }
        }
    }
    for ($i = $L; $i <= $R; $i++) {
        if (!isset($problemQueries[$i])) {
            throw new Exception("خطا در دریافت اطلاعات سوال " . chr($i + ord('A')));
        }
    }

    for ($i = $L; $i <= $R; $i++) {
        $data = $api->getProblemArrayData($problemQueries[$i]);
        if (!isset($data['rating'])) {
            $data['rating'] = $_POST['defaultDifficulty'];
        }
        $problemId = problemset::addProblem($problemQueries[$i],
            array_values(array_unique(array_merge(json_decode($data['tags'], true), $additionalTags)))
            , (int)$data['rating'], (float)$_POST["prior"], false);
        problemset::addUserLiked(strtolower($_POST["username"]), $problemId);
    }
} catch (Exception $e) {
    echo "<h3 dir=\"rtl\"> خطا: " . $e->getMessage();
}
?>


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
    <input style="width: 20em;" name="fromProblem" value="A" placeholder="from problem index"><br>
    <input style="width: 20em;" name="toProblem" value="F" placeholder="to problem index"><br>
    <input style="width: 20em;" name="prior" value="" placeholder="prior"><br>
    <input style="width: 20em;" name="additionalTags" value=""
           placeholder="additional tags (split tags by , )"><br>
    <input style="width: 20em;" name="defaultDifficulty" value="2400" placeholder="default difficulty"><br>
    <input class="submit" type="submit" value="Submit">
</form>
<h4 dir="rtl">
    لیست تگ ها:<br><br>
    <?php
    echo implode("\n<br> ", json_decode(file_get_contents("data/allTags.txt"), true));
    ?>
</h4>
</body>
</html>
