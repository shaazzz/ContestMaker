<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/data/defines.php';
require __DIR__ . '/Models/CodeforcesApi.php';
require __DIR__ . '/Models/problemset.php';

problemset::readFromFile();
problemset::resetUserSolved();
$cfApi = new CodeforcesApi();

$names = json_decode(file_get_contents(realpath("data/legends.txt")), true);
$legends = $names["normal"];

$isSuper = array();
foreach ($names["super"] as $person) {
    $isSuper[$person] = true;
}

$CNT = array();
$MAX = 0;

foreach ($legends as $person) {
    $seen = array();
    $userRate = $cfApi->request("user.info", array("handles" => $person))['result'][0]['rating'];
    if ((int)$userRate < 1900) {
        continue;
    }
    $submissions = $cfApi->request("user.status", array("handle" => $person))['result'];
    echo $person . " has " . count($submissions) . " submissions\n";
    foreach ($submissions as $sub) {
        if ($sub["verdict"] == "OK") {
            if (!isset($sub["problem"]["contestId"]) || !isset($sub["problem"]["tags"]) || !isset($sub["problem"]["index"])) {
                continue;
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
                    0, false, 0, 0, null, true);
            }
            if (!isset($seen[$sub["id"]])) {
                if (isset($isSuper[$person])) {
                    if (!isset($CNT[$sub["id"]]))
                        $CNT[$sub["id"]] = 0;
                    $CNT[$sub["id"]]++;
                    $MAX = max($MAX, $CNT[$sub["id"]]);
                }
                problemset::addUserSolved($sub["id"], true);
                $seen[$sub["id"]] = true;
            }
        }
    }
    unset($seen);
}

$MAX_PRIOR = 0.2;
foreach ($CNT as $problemId => $accepted) {
    problemset::$problems[$problemId]->changePrior(($accepted / $MAX) * $MAX_PRIOR);
}

//shuffle(problemset::$problems);
problemset::update();

