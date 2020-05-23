<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
libxml_use_internal_errors(true);


require_once 'data/defines.php';


// have bugs
function getSize($block, $scr)
{
    $ans = 1000;
    foreach ($scr as $username => $solved) {
        $ans = min($ans, count($solved));
    }
    return min(7 * $block, $ans);
}


$api = new CodeforcesUserApi();
$api->login(CODEFORCES_USERNAME, CODEFORCES_PASSWORD);
$sc = array();
$contestCof = array(10, 17, 26, 37); // changed
AllContests::readFromFile();
foreach (AllContests::$contests as $weekId => $weekContests) {
    echo "Starting week " . $weekId . "...\n";
    foreach ($weekContests as $key => $contest) {
        $sc[$key] = $api->getScoreboard($contest->contestId);
        $block = count($contest->getDifficulties());
        $size = getSize($block, $sc[$key]);
        echo "contest $key has block size equal to $block and $size problems\n";
        if ($size % $block != 0) {
            throw new Exception("(size % block) should be 0");
        }
    }
    for ($i = 0; $i < 7; $i++) {
        echo "Starting day " . ($i + 1) . "...\n";
        $index = 0;
        AllUsers::startOftheDay();
        foreach ($weekContests as $key => $contest) {
            $block = count($contest->getDifficulties());
            $size = getSize($block, $sc[$key]);
            if (($i + 1) * $block <= $size) {
                AllUsers::updateRatings($sc[$key], $contestCof[$index], $i * $block, ($i + 1) * $block);
            } else {
                echo "day " . $i . " not exist in contest " . $contest->contestId . "\n";
            }
            $index++;
        }
        AllUsers::endOftheDay();
    }
}

?>