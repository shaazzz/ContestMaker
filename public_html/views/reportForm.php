<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>فرم گزارش کاربران</title>
    <link rel="icon" href="//training.shaazzz.ir/files/shaazzzLogo.png">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v26.0.2/dist/font-face.css" rel="stylesheet" type="text/css" />
    <link href="//training.shaazzz.ir/files/styles.css" rel="stylesheet" type="text/css">
</head>


<body>
<noscript>Sorry, your browser does not support JavaScript!</noscript>

<div class="container">
    <form id="contact" action="report.php" method="post" enctype="multipart/form-data">
        <h3 dir="rtl">فرم گزارش کاربران</h3>
        <?php
        $mx = intdiv((int)file_get_contents("../data/counter.txt") - 1, 7);
        if ($mx < 1) {
            die("<br><h4 dir='rtl'>هنوز کانتستی برای گزارش وجود ندارد!<h4>");
        }
        ?>
        <br>
        <fieldset>
            <input type="number" name="weekNumber" value="1" min="1" max=
            <?php
            echo $mx;
            ?> placeholder="Contest Id" tabindex="3" required>
            <h4 dir="rtl"><b>توضیحات: </b> شماره هفته برای گزارش <br></h4>
        </fieldset>
        <fieldset>
            <input type="file" name="config" accept="application/JSON" required>
            <h4 dir="rtl"><b>توضیحات: </b>فایل Config<br></h4>
        </fieldset>
        <fieldset>
            <button class="submit" type="submit">Submit</button>
        </fieldset>
    </form>

</div>
</body>
</html>
