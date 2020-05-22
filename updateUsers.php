<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
libxml_use_internal_errors(true);

require __DIR__ . '/Models/AllUsers.php';
require __DIR__ . '/Models/CodeforcesUserApi.php';
require __DIR__ . '/data/defines.php';
//AllUsers::readFromFile();

$api = new CodeforcesUserApi();
$api->login(CODEFORCES_USERNAME, CODEFORCES_PASSWORD);
$contestIds = array(278840, 278841, 278842, 278843, 279670, 279671, 279777, 279778, 280426, 280427, 280428, 280429);
$contestLen = array(15, 15, 15, 15, 18, 18, 15, 15, 3, 18, 18, 18); // bigginer should be complete
$sc = array(0, 0, 0, 0);
$contestCof = array(10, 20, 30, 40);

for ($week = 0; $week < 3; $week++) {
    for ($i = 0; $i < 4; $i++)
        $sc[$i] = $api->getScoreboard($contestIds[$week * 4 + $i]);
    for ($i = 0; $i < 7; $i++) {
        for ($j = 0; $j < 4; $j++) {
            if ($contestLen[$week * 4 + $j] <= 3 * $i)
                continue;
            AllUsers::updateRatings($sc[$j], $contestCof[$j], $i * 3, $i * 3 + 3);
        }
        AllUsers::endOftheDay();
    }
}
/*
for($i = 0; $i < count($contestIds); $i++){
    $id = $contestIds[$i];
    $ln = $contestLen[$i];
    $sc = $api->getScoreboard($id);
    for ($j = 0; $j < $ln; $j += 3) {
        AllUsers::updateRatings($sc, 10, $j, $j + 3);
        AllUsers::endOftheDay();
    }
}
*/
?>