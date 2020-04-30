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
    <form action="addPrivateContest.php">
        <input style="width: 20em;" name="username" value="" placeholder="username"><br>
        <input style="width: 20em;" name="password" value="" placeholder="password"><br>
        <input style="width: 20em;" name="contestAddressPrefix" value="gym" placeholder="contest address prefix"><br>
        <input style="width: 20em;" name="contestId" value="" placeholder="contest id"><br>
        <input style="width: 20em;" name="stageNumber" value="" placeholder="stage number"><br>
        <input style="width: 20em;" name="L" value="0" placeholder="from problem number 0-base [L,R)"><br>
        <input style="width: 20em;" name="R" value="26" placeholder="to problem number 0-base [L,R)"><br>
        <input style="width: 20em;" name="prior" value="" placeholder="prior"><br>
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