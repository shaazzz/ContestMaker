<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/CodeforcesApi.php';
use \API\CodeforcesApi;

$api = new CodeforcesApi();

$api->addUser("ShaazzzAdmin", "","");

$parameters = array(
    "contestId" => 566,
    "from" => 1,
    "count" => 5,
    "showUnofficial" => true
);

echo $api->request("contest.standings", $parameters);