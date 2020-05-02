<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/data/defines.php';
require __DIR__ . '/CodeforcesApi.php';
require __DIR__ . '/Models/problemset.php';

problemset::readFromFile();
problemset::resetUserSolved();
$cfApi = new CodeforcesApi();

$legends = json_decode(file_get_contents("data/legends.txt"), true);
$seen = array();


foreach ($legends as $person) {
    $userRate = $cfApi->request("user.info", array("handles" => $person))['result'][0]['rating'];
    if ((int)$userRate < 1900) {
        continue;
    }
    $submitions = $cfApi->request("user.status", array("handle" => $person))['result'];
    echo $person . " has " . count($submitions) . " submitions\n";
    foreach ($submitions as $sub) {
        if ($sub["verdict"] == "OK") {
            if(!isset($sub["problem"]["contestId"])) {
                var_dump($sub);
            }
            $sub["id"] = $sub["problem"]["contestId"] . $sub["problem"]["index"];
            if (!isset(problemset::$problems[$sub["id"]])) {
                if (!isset($sub["problem"]["rating"])) {
                    continue;
                }
                problemset::addProblem(
                    $sub["id"],
                    $sub["problem"]["tags"],
                    $sub["problem"]["rating"],
                    0, false, true);
            }
            if (!isset($seen[$person][$sub["id"]])) {
                problemset::addUserSolved($sub["id"], true);
                $seen[$person][$sub["id"]] = true;
            }
        }
    }
}
echo "\n<br> maxAccepted: " . problemset::$maxAccepted;
echo "\n<br> maxLike: " . problemset::$maxLike;
problemset::update();

