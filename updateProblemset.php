<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/data/defines.php';
require __DIR__ . '/CodeforcesApi.php';
require __DIR__ . '/Models/problemset.php';

problemset::readFromFile();
problemset::resetUserSolved();
$cfApi = new CodeforcesApi();

$legends = json_decode(file_get_contents(realpath("data/legends.txt")), true);


foreach ($legends as $person) {
    $seen = array();
    $userRate = $cfApi->request("user.info", array("handles" => $person))['result'][0]['rating'];
    if ((int)$userRate < 1900) {
        continue;
    }
    $submitions = $cfApi->request("user.status", array("handle" => $person))['result'];
    echo $person . " has " . count($submitions) . " submitions\n";
    foreach ($submitions as $sub) {
        if ($sub["verdict"] == "OK") {
            if (!isset($sub["problem"]["contestId"]) || !isset($sub["problem"]["tags"]) || !isset($sub["problem"]["index"])) {
                continue;
            }
            $sub["id"] = $sub["problem"]["contestId"] . $sub["problem"]["index"];
            if (!isset(problemset::$problems[$sub["id"]])) {
                if (!isset($sub["problem"]["rating"])) {
                    continue;
                }
                if($person == "Shayan.P")
                   array_push($sub["problem"]["tags"], "Shayan.P");
                    problemset::addProblem(
                        $sub["id"],
                        $sub["problem"]["tags"],
                        $sub["problem"]["rating"],
                        0, false, 0, 0, null, true);
            }
            if (!isset($seen[$sub["id"]])) {
                problemset::addUserSolved($sub["id"], true);
                $seen[$sub["id"]] = true;
            }
        }
    }
    unset($seen);
}
//shuffle(problemset::$problems);
problemset::update();

