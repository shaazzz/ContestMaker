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

date_default_timezone_set('Asia/Tehran');
problemset::readFromFile();
problemset::resetUsed();
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
    $setting = json_decode(file_get_contents("data/weekContestSettings.txt"), true);
    $cntProblems = $dayNumber % 7 > 1 ? 4 : 3;
    AllContests::addContest($contestIndex, CONTEST_LEVEL0 . "",(int)$setting[CONTEST_LEVEL0]['L'],
        (int)$setting[CONTEST_LEVEL0]['R'], $cntProblems, $setting[CONTEST_LEVEL0]['tags'], null, $api);

    AllContests::addContest($contestIndex, CONTEST_LEVEL1 . "", (int)$setting[CONTEST_LEVEL1]['L'],
        (int)$setting[CONTEST_LEVEL1]['R'], $cntProblems, $setting[CONTEST_LEVEL1]['tags'], null, $api);

    AllContests::addContest($contestIndex, CONTEST_LEVEL2 . "", (int)$setting[CONTEST_LEVEL2]['L'],
        (int)$setting[CONTEST_LEVEL2]['R'], $cntProblems, $setting[CONTEST_LEVEL2]['tags'], null, $api);

    AllContests::addContest($contestIndex, CONTEST_LEVEL3 . "", (int)$setting[CONTEST_LEVEL3]['L'],
        (int)$setting[CONTEST_LEVEL3]['R'], $cntProblems, $setting[CONTEST_LEVEL3]['tags'], null, $api);
}

foreach (AllContests::$contests[$contestIndex] as $contest) {
    var_dump($contest->giveContest());
    $api->setNewProblemsForContest($contest, $contest->giveContest());
}
