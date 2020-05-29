<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
libxml_use_internal_errors(true);

date_default_timezone_set('Asia/Taipei');

require_once 'data/defines.php';

problemset::readFromFile();
AllContests::readFromFile();

$api = new CodeforcesUserApi();
$api->login(CODEFORCES_USERNAME, CODEFORCES_PASSWORD);
$cfApi = new CodeforcesApi();


//foreach (AllContests::$contests[4] as $contest) {
    $api->setVisibilityProblems(281384, false);
//}

//require "updateUsers.php";