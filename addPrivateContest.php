</<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Document</title>
</head>
<body>
    <h3 dir="rtl">اگر میخواهید با اکانت پیشفرض سوال ها اضافه شوند
        api key و api secret
        را خالی بگذارید</h3>
    <form action="addPrivateContest.php">
        <input style="width: 20em;" name="apiKey" value="" placeholder="api key">
        <input style="width: 20em;" name="apiSecret" value="" placeholder="api secret">
        <input style="width: 20em;" name="username" value="" placeholder="username">
        <input style="width: 20em;" name="contestId" value="" placeholder="contest id">
        <input style="width: 20em;" name="stageNumber" value="" placeholder="stage number">
        <input style="width: 20em;" name="prior" value="" placeholder="prior">
        <input class="submit" type="submit" value="Submit">
    </form>
</body>
</html>

<?php
    $inputs=array("apiKey","apiSecret","username","contestId","stageNumber","prior");
    foreach ($inputs as $input){
        if(!isset($_POST[$input])){
            die();
        }
    }


?>