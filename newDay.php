<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Taipei');

require __DIR__ . '/Models/contest.php';
require __DIR__ . '/Models/AllContests.php';
require __DIR__ . '/data/defines.php';
require __DIR__ . '/CodeforcesUserApi.php';

AllContests::readFromFile();

$api = new CodeforcesUserApi();
$api->login(CODEFORCES_USERNAME, CODEFORCES_PASSWORD);

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
    $setting = json_decode(file_get_contents("data/weekContestSettings.txt"), true);
    $contestSettings = null;
    if (isset($setting["Week" . $contestIndex])) {
        $contestSettings = $setting["Week" . $contestIndex];
    } else {
        echo "week setting not found! using week default setting";
        $contestSettings = $setting["WeekDefault"];
    }

    $cntProblems = 3;
    foreach ($contestSettings as $key => $value) {
        AllContests::addContest($contestIndex, $key, (int)$contestSettings[$key]['L'],
            (int)$contestSettings[$key]['R'], $cntProblems, $contestSettings[$key]['tags'], null, $api);
    }
}

foreach (AllContests::$contests[$contestIndex] as $contest) {
    $api->setNewProblemsForContest($contest, $contest->giveContest());
}
