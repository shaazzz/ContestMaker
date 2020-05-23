<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
libxml_use_internal_errors(true);


require_once 'data/defines.php';


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
    $used = true;
    for ($i = 0; $i < 7; $i++) {
        echo "Starting day " . ($i + 1) . "...\n";
        $index = 0;
        if ($used) {
            AllUsers::startOftheDay();
        }
        $used = false;
        foreach ($weekContests as $key => $contest) {
            $block = count($contest->getDifficulties());
            $size = getSize($block, $sc[$key]);
            if (($i + 1) * $block <= $size) {
                AllUsers::updateRatings($sc[$key], $contestCof[$index], $i * $block, ($i + 1) * $block);
                $used = true;
            } else {
                echo "day " . $i . " not exist in contest " . $contest->contestId . "\n";
            }
            $index++;
        }
        if ($used) {
            AllUsers::endOftheDay();
        }
    }
}

?>