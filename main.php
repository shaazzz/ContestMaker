<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/CodeforcesApi.php';
require __DIR__ . '/data/defines.php';

$api = new CodeforcesApi();

$api->addUser(CODEFORCES_USERNAME, CODEFORCES_API_KEY, CODEFORCES_API_SECRET);

$parameters = array(
    "contestId" => 277992,
    "from" => 1,
    "count" => 5,
    "showUnofficial" => true
);

print_r($api->request("contest.standings", $parameters));
