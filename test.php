<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
libxml_use_internal_errors(true);

date_default_timezone_set('Asia/Taipei');

require_once 'data/defines.php';

$api = new CodeforcesUserApi();
$api->login(CODEFORCES_USERNAME, CODEFORCES_PASSWORD);
$cfApi = new CodeforcesApi();

//$sc = $api->getScoreboard(280427, "group/W2YvE0cOoh/contest");
///var_dump($sc);

$users = $api->getActiveParticipates(280427, "group/W2YvE0cOoh/contest");
var_dump($users);
echo count($cfApi->getForbiddenProblemIds($users));//,"group/W2YvE0cOoh/contest"));

