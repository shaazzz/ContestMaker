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
    $submitions = json_decode($cfApi->request("user.status", array("handle" => $person))['result'], true);

    echo count($submitions)."<br>";/*
    foreach ($submitions as $sub) {
        if ($sub["verdict"] == "OK") {

            /*if ($seen[$sub["id"]] == null) {
                $seen[$sub["id"]] = true;
                problemset::addProblem($sub["id"],
                    $sub["problem"]["contestId"] . $sub["problem"]["index"],
                    $sub["problem"]["tags"],
                    $sub["problem"]["rating"],
                    0, false, true);
            }
            problemset::addUserSolved($sub["id"], true);
        }
    }*/
}
problemset::update();
