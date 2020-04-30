<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/CodeforcesUserApi.php';
require __DIR__ . '/data/defines.php';

$api = new CodeforcesUserApi();


//$api->login(CODEFORCES_USERNAME, CODEFORCES_PASSWORD);

//$api->addProblemsToContest(278072,array("50A"));
//$api->setVisibilityProblems(278072, array(608415), false);
//$api->addProblemsToContest(278072,array("60C"));
$api->getScoreboard();