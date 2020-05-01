<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/Models/contest.php';
require __DIR__ . '/data/defines.php';
require __DIR__ . '/CodeforcesUserApi.php';


$api = new CodeforcesUserApi();
$api->login(CODEFORCES_USERNAME, CODEFORCES_PASSWORD);

$contest = new contest(278432, 34, "Candidate masters", 1900, 2400, 3);//$api->createNewMashup(13, CONTEST_LEVEL1);
$api->changeTimeToToday($contest);


/*
date_default_timezone_set('Asia/Taipei');
problemset::readFromFile();

$dayNumber = 0;
if (file_exists("data/counter.txt")) {
    $dayNumber = (int)file_get_contents("data/counter.txt");
}
file_put_contents("data/counter.txt", $dayNumber + 1);
$contestIndex = intdiv($dayNumber, 7) + 1;
$contests = array(
    new contest(278320, $contestIndex, "Beginners", 700, 1400, $dayNumber % 7 < 5 ? 4 : 3),
    new contest(278321, $contestIndex, "Specialist", 1400, 2000, 3),
    new contest(278324, $contestIndex, "Candidate masters", 1900, 2400, 3),
    new contest(278323, $contestIndex, "Grandmasters", 2500, 3500, 3),
);


if ($dayNumber % 7 == 0) {
    foreach ($contests as $contest) {
        $api->sendScoreboard($contest->contestId, CF_GROUP_PREFIX_ADDRESS);
    }
} else {
    foreach ($contests as $contest) {
        $api->setNewProblemsForContest($contest, $contest->giveContest());
    }
}
*/