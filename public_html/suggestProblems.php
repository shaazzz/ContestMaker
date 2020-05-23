<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>فرم پیشنهاد سوال</title>
    <link href="styles.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v26.0.2/dist/font-face.css" rel="stylesheet" type="text/css" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
</head>


<body>
<noscript>Sorry, your browser does not support JavaScript!</noscript>

<div class="container">

    <form id="contact" action="suggestProblems.php" method="post">
        <h3 dir="rtl">فرم پیشنهاد سوال</h3>
        <script type="text/javascript">
            $(document).ready(function () {
                $('.js-example-basic-multiple').select2();
            });
        </script>

        <br>
        <?php
        $inputs = array("username", "password", "contestAddressPrefix", "contestId", "defaultDifficulty", "fromProblem", "toProblem", "prior");

        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        try {
            chdir('..');
            if (isset($_POST['token'])) {
                $_GET['token'] = $_POST['token'];
            }
            if (!isset($_GET['token']) || hash("sha512", $_GET['token']) != file_get_contents("data/suggestProblemPass.txt")) {
                die("<h4 dir='rtl'>توکن اشتباه است</h4>");
            }
            require __DIR__ . '/../data/defines.php';
            require __DIR__ . '/../Models/problemset.php';
            require __DIR__ . '/../Models/CodeforcesUserApi.php';

            foreach ($inputs as $input) {
                if (!isset($_POST[$input])) {
                    throw new Exception("_POST input error");
                }
            }
            echo "<h4 dir='rtl'> <b>شروع عملیات...</b></h4><br>";
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
                throw new Exception("(from problem) به درستی وارد نشده است!");
            }
            if ($R < $L || $R >= 26) {
                throw new Exception("(to problem) به درستی وارد نشده است!");
            }
            $problemQueries = $api->getContestProblemQueries($_POST["contestId"], $_POST["contestAddressPrefix"]);
            problemset::readFromFile();
            $allTags = json_decode(file_get_contents("data/allTags.txt"), true);
            $additionalTags = array();
            if (isset($_POST['additionalTags'])) {
                $additionalTags = $_POST['additionalTags'];
            }
            for ($i = $L; $i <= $R; $i++) {
                if (!isset($problemQueries[$i])) {
                    throw new Exception("خطا در دریافت اطلاعات سوال " . chr($i + ord('A')) . "<br>دسترسی خود را به کانتست بررسی کنید");
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
                if (strlen($_POST["username"]) > 0) {
                    problemset::addUserLiked(strtolower($_POST["username"]), $problemId);
                }
            }
            echo "<br>";
        } catch (Exception $e) {
            if ($e->getMessage() != "_POST input error") {
                echo "<errorbox><h4 dir=\"rtl\"> <b>خطا:</b> " . $e->getMessage() . "</h4></errorbox><br>";
            }
        }
        ?>
        <fieldset>
            <input name="username" type="text" value="" placeholder="Codeforces Username" tabindex="1" autofocus>
            <input name="password" value="" type="password" placeholder="Codeforces Password" tabindex="2">

            <h4 dir="rtl">
                <b>توضیحات: </b>
                اگر میخواهید با اکانت پیشفرض سوال ها اضافه شوند این دو ورودی را خالی بگذارید
                <br>
                برای اضافه کردن سوال از گروه ها و مشاپ ها نیاز است که نقش Manager را در آن ها داشته باشید و این دو
                وروری
                را وارد کنید
                همچنین برای ثبت لایک سوالات این دو ورودی ضروری است

                <br>
                <b>نکته: </b>
                ما یوزرنیم و پسورد شما را ذخیره نمیکنیم, میتوانید کدهای این صفحه را از
                <a href="https://github.com/shaazzz/ContestMaker/blob/master/public_html/">اینجا</a>
                ببینید
            </h4>
        </fieldset>
        <fieldset>
            http://codeforces.com/[Contest Path]/[Contest Id]
            <input type="text" name="contestAddressPrefix" value="gym" placeholder="Contest Path" tabindex="3"
                   required>
            <h4 dir="rtl"><b>توضیحات: </b> آدرس کانتست: <br>برای مثال:
                <br>gym<br>contest<br>group/W2YvE0cOoh/contest<br></h4>
        </fieldset>
        <fieldset>
            <input type="number" name="contestId" value="" placeholder="Contest Id" tabindex="3" required>
            <h4 dir="rtl"><b>توضیحات: </b> شناسه کانتست <br>برای مثال: <br>842<br>280426<br></h4>
        </fieldset>
        <fieldset>
            <input type="text" name="fromProblem" value="A" placeholder="From Problem" tabindex="4" pattern="[A-Z]{1}"
                   required>
            <input type="text" name="toProblem" value="F" placeholder="To Problem" tabindex="5" pattern="[A-Z]{1}"
                   required>
            <h4 dir="rtl"><b>توضیحات: </b> بازه سوالاتی که قصد اضافه کردن آنها را دارید<br></h4>

        </fieldset>
        <fieldset>
            <input name="prior" value="0.2" step="0.01" type="number" min="0" max="0.5"
                   placeholder="Learning Level" tabindex="6" required>
            <h4 dir="rtl"><b>توضیحات: </b> میزان جذابیت و آموزنده بودن سوال<br>باید عددی بین ۰ و ۰.۵ باشد</h4>
        </fieldset>
        <fieldset>
            <select class="js-example-basic-multiple" name="additionalTags[]" multiple="multiple" tabindex="7">
                <?php
                $array = json_decode(file_get_contents("data/allTags.txt"), true);
                foreach ($array as $tag) {
                    echo "<option name='$tag'>$tag</option>";
                }
                ?>
            </select>
            <h4 dir="rtl"><b>توضیحات: </b> تگ های اضافی<br></h4>
        </fieldset>
        <fieldset>
            <input name="defaultDifficulty" type="number" min="700" max="3800" step="100" value="2400"
                   placeholder="Default Difficulty" tabindex="8" required>
            <h4 dir="rtl"><b>توضیحات: </b> سختی پیشفرض سوالات<br>اگر سوالی در Codeforces دارای سختی نبود, ازین مقدار
                استفاده میشود<br></h4>
        </fieldset>
        <fieldset>
            <button class="submit" type="submit">Submit</button>
        </fieldset>
        <input hidden name="token" value=<?php echo $_GET['token'] ?>>
    </form>

</div>
</body>
</html>
