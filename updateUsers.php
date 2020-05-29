<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
libxml_use_internal_errors(true);


require_once 'data/defines.php';


$cfApi = new CodeforcesApi();
$cfApi->addUser("shaazzz_admin", CODEFORCES_API_KEY, CODEFORCES_API_SECRET);


// have bugs
function getSize($block, $scr)
{
    foreach ($scr as $username => $solved) {
        return count($solved);
    }
    return 7 * $block;
}


$api = new CodeforcesUserApi();
$api->login(CODEFORCES_USERNAME, CODEFORCES_PASSWORD);
$sc = array();
$contestCof = array(7, 18, 30, 42); // changed
AllContests::readFromFile();
$size = array();
$dayNumber = (int)file_get_contents("data/counter.txt");
foreach (AllContests::$contests as $weekId => $weekContests) {
    echo "Starting week " . $weekId . "...\n";
    foreach ($weekContests as $key => $contest) {
        $sc[$key] = $api->getScoreboard($contest->contestId);
        $block = count($contest->getDifficulties());
        $size[$key] = count($cfApi->getContestProblems($contest->contestId));
        echo "contest $key has block size equal to $block and $size[$key] problems\n";
        if ($size[$key] % $block != 0) {
            throw new Exception("(size % block) should be 0");
        }
    }
    for ($i = 0; $i < 7; $i++) {
        if (($weekId - 1) * 7 + ($i + 1) >= $dayNumber) {
            continue;
        }
        echo "Starting day " . ($i + 1) . "...\n";
        $index = 0;
        AllUsers::startOftheDay();
        $used = false;
        foreach ($weekContests as $key => $contest) {

            $block = count($contest->getDifficulties());
            if (($i + 1) * $block <= $size[$key]) {
                AllUsers::updateRatings($sc[$key], $contestCof[$index], $i * $block, ($i + 1) * $block);
                $used = true;
            } else {
                echo "day " . $i . " not exist in contest " . $contest->contestId . "\n";
            }
            $index++;
        }
        AllUsers::endOftheDay();
    }
}

?>