<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/CodeforcesUserApi.php';

$api = new CodeforcesUserApi();


$api->login("shaazzz_admin", "passss");

//$api->addProblemsToContest(278072,array("50A"));
