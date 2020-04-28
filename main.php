<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/CodeforcesApi.php';

$api = new CodeforcesApi();

$api->addUser("ShaazzzAdmin", "74549d19b172095859d90d556499fa9c6c45db3f", "6528f3887d3dbd195c8a85bdf38f4d54f37a8017");

$parameters = array(
    "contestId" => 277992,
    "from" => 1,
    "count" => 5,
    "showUnofficial" => true
);

echo $api->request("contest.standings", $parameters);
