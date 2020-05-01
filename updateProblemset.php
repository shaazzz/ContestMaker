<?php

require __DIR__ . '/CodeforcesApi.php';
require __DIR__ . '/problemset.php';

problemset::readFromFile();

$api = new CodeforcesApi();

$legends = file_get_contents("data/legends.txt");
$seen = array();

function go(){
    for($legends as $person){
        $submitions = $api->request("user.status", array("handle"=>$person))['result'];
        for($submitions as $sub){
            if($sub["verdict"] == "OK"){
                if($seen[$sub["id"]] == null){
                    $seen[$sub["id"]] = true;
                    problemset::addProblem($sub["id"],
                        $sub["problem"]["contestId"].$sub["problem"]["index"],
                        $sub["problem"]["tags"],
                        $sub["problem"]["rating"],
                        0,
                        false);
                }
                problemset::addUserSolved($sub[$id]);
            }
        }
    }
}