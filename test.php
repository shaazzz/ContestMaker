<?php


ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Asia/Taipei');

require __DIR__ . '/Models/contest.php';
require __DIR__ . '/Models/AllContests.php';
require __DIR__ . '/data/defines.php';
require __DIR__ . '/Models/CodeforcesUserApi.php';

$api = new CodeforcesUserApi();
$api->login(CODEFORCES_USERNAME, CODEFORCES_PASSWORD);

var_dump($api->getParticipates(279777));