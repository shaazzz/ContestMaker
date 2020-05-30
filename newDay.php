<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
libxml_use_internal_errors(true);

try {

    date_default_timezone_set('Asia/Taipei');

    require_once 'data/defines.php';

    problemset::readFromFile();
    AllContests::readFromFile();

    $api = new CodeforcesUserApi();
    $api->login(CODEFORCES_USERNAME, CODEFORCES_PASSWORD);
    $cfApi = new CodeforcesApi();

    $dayNumber = 0;
    if (file_exists("data/counter.txt")) {
        $dayNumber = (int)file_get_contents("data/counter.txt");
    }
    file_put_contents("data/counter.txt", $dayNumber + 1);
    $contestIndex = intdiv($dayNumber, 7) + 1;

    $setting = json_decode(file_get_contents("data/weekContestSettings.txt"), true);
    if ($dayNumber % 7 == 0) {
        try {
            if ($contestIndex - 1 > 0) {
                if (!isset(AllContests::$contests[$contestIndex - 1])) {
                    throw new Exception("");
                }
                foreach (AllContests::$contests[$contestIndex - 1] as $contest) {
                    $api->setVisibilityProblems($contest->contestId, true);
                    //$api->sendScoreboard($contest->contestId, CF_GROUP_PREFIX_ADDRESS);
                }
            }
        } catch (Exception $e) {
            echo "\n<br>error in sending scoreboard";
        }
        if (isset($setting["Week" . $contestIndex])) {
            $contestSettings = $setting["Week" . $contestIndex];
        } else {
            echo "week setting not found! using week default setting";
            $contestSettings = $setting["WeekDefault"];
        }

        foreach ($contestSettings as $key => $value) {
            if (!isset($contestSettings[$key]['workingDays'])) {
                $contestSettings[$key]['workingDays'] = "1111111";
            }
            AllContests::addContest($contestIndex, $key, $contestSettings[$key]['difficulties'], $contestSettings[$key]['workingDays'],
                $contestSettings[$key]['tags'], $contestSettings[$key]['negativeTags'], null, $api);
        }
    }

    foreach (AllContests::$contests[$contestIndex] as $contest) {
        $api->setVisibilityProblems($contest->contestId, true);
    }

    require "updateUsers.php";

    foreach (AllContests::$contests[$contestIndex] as $contest) {
        if (isset($setting["Week" . $contestIndex])) {
            $contestSettings = $setting["Week" . $contestIndex][$contest->getContestLevel()];
        } else {
            file_put_contents('/data/log.txt', "week setting not found! using week default setting...\n", FILE_APPEND);
            $contestSettings = $setting["WeekDefault"][$contest->getContestLevel()];
        }
        $forbiddenUsers = $api->getActiveParticipates($contest->contestId);

        file_put_contents('/data/log.txt', "(" . implode(', ', $forbiddenUsers) . ") are active users for contest " . $contest->contestId . "\n", FILE_APPEND);
        $forbiddenProblemIds = $cfApi->getForbiddenProblemIds($forbiddenUsers);
        file_put_contents('/data/log.txt', "number of forbidden problem:" . count($forbiddenProblemIds) . "\n" . $contest->contestId . "\n", FILE_APPEND);

        if (isset($contestSettings["hideProblemsEveryDay"]) && $contestSettings["hideProblemsEveryDay"]) {
            $api->setVisibilityProblems($contest->contestId, false);
        }
        if ($contest->isWorking($dayNumber - 1)) {
            $api->setNewProblemsForContest($contest, $contest->giveContest($forbiddenProblemIds));
        }
    }
} catch (Exception $e) {
    echo "<h3 dir=\"rtl\"> خطا: " . $e->getMessage();
}
