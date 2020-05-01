<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/Models/contest.php';
require __DIR__ . '/Models/AllContests.php';
require __DIR__ . '/data/defines.php';
require __DIR__ . '/CodeforcesUserApi.php';

AllContests::readFromFile();

$api = new CodeforcesUserApi();
$api->login(CODEFORCES_USERNAME, CODEFORCES_PASSWORD);
/*
AllContests::addContest(34, "Candidate masters", 1900, 2400, 3, null, 278442);
echo AllContests::$contests[34]["Candidate masters"]->contestId;
*/
date_default_timezone_set('Asia/Taipei');
problemset::readFromFile();

$dayNumber = 0;
if (file_exists("data/counter.txt")) {
    $dayNumber = (int)file_get_contents("data/counter.txt");
}
file_put_contents("data/counter.txt", $dayNumber + 1);
$contestIndex = intdiv($dayNumber, 7) + 1;


if ($dayNumber % 7 == 0) {
    if ($contestIndex - 1 > 0) {
        foreach (AllContests::$contests[$contestIndex - 1] as $contest) {
            $api->sendScoreboard($contest->contestId, CF_GROUP_PREFIX_ADDRESS);
        }
    }
    AllContests::addContest($contestIndex, CONTEST_LEVEL0 . "", 700, 1400, $dayNumber % 7 < 5 ? 4 : 3, array("implementation","greedy"), null, $api);
    AllContests::addContest($contestIndex, CONTEST_LEVEL1 . "", 1400, 2000, 3, null, null, $api);
    AllContests::addContest($contestIndex, CONTEST_LEVEL2 . "", 1900, 2400, 3, null, null, $api);
    AllContests::addContest($contestIndex, CONTEST_LEVEL3 . "", 2500, 3500, 3, null, null, $api);
}
var_dump(AllContests::$contests);
var_dump(AllContests::$contests[$contestIndex]);
var_dump(AllContests::$contests[$contestIndex.""]);

foreach (AllContests::$contests[$contestIndex] as $contest) {
    var_dump($contest->giveContest());
    $api->setNewProblemsForContest($contest, $contest->giveContest());
}