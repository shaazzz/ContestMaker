<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/data/defines.php';
require __DIR__ . '/CodeforcesApi.php';
require __DIR__ . '/Models/problemset.php';

problemset::readFromFile();

$cfApi = new CodeforcesApi();

$legends = json_decode(file_get_contents("data/legends.txt"), true);
$seen = array();
foreach ($legends as $person) {
    $submitions = $cfApi->request("user.status", array("handle" => $person))['result'];
    echo $person . " has " . count($submitions) . " submitions\n";
    foreach ($submitions as $sub) {
        if ($sub["verdict"] == "OK") {
            if (!isset(problemset::$problems[$sub["id"]])) {
                //$seen[$sub["id"]] = true;
                if (!isset($sub["problem"]["rating"])) {
                    continue;
                }
                if ($sub["id"] == "6357165") {
                    var_dump($sub);
                }
                $sub["id"] = $sub["problem"]["contestId"] . $sub["problem"]["index"];
                problemset::addProblem(
                    $sub["id"],
                    $sub["problem"]["tags"],
                    $sub["problem"]["rating"],
                    0, false, true);
            }
            problemset::addUserSolved($sub["id"], true);
        }
    }
}
problemset::update();

