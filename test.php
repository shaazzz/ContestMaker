<?php


ini_set('display_errors', 1);
error_reporting(E_ALL);
libxml_use_internal_errors(true);

date_default_timezone_set('Asia/Taipei');

require __DIR__ . '/Models/contest.php';
require __DIR__ . '/Models/AllContests.php';
require __DIR__ . '/data/defines.php';
require __DIR__ . '/Models/CodeforcesUserApi.php';
require_once __DIR__ . '/Models/CodeforcesApi.php';

$api = new CodeforcesUserApi();
$api->login(CODEFORCES_USERNAME, CODEFORCES_PASSWORD);
$cfApi = new CodeforcesApi();

var_dump($cfApi->getForbiddenProblemIds($api->getActiveParticipates(279670)));//,"group/W2YvE0cOoh/contest"));