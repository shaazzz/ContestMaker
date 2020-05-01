<?php

require __DIR__ . '/problem.php';

class problemset
{
    static $problems = array();
    static $maxLike = 0, $maxAccepted = 0;

    static function readFromFile()
    {
        if (file_exists("data/data.txt")) {
            $data = json_decode(file_get_contents("data/data.txt"), true);
            foreach ($data as $problemJson) {
                problemset::addProblem($problemJson["problemId"], $problemJson["problemName"], $problemJson["tags"],
                    $problemJson["difficulty"], $problemJson["prior"]);
            }
        }
    }

    static function update()
    {
        file_put_contents("data/data.txt", json_encode(problemset::$problems));
    }

    static function addProblem($problemId, $problemName, $tags, $difficulty, $prior = 0, $inside = false)
    {
        if (!isset(problemset::$problems[$problemId])) {
            problemset::$problems[$problemId] = new problem($problemId, $problemName, $tags, $difficulty, $prior);
        }
        if (!$inside) {
            problemset::update();
        }
    }

    function addUserSolved($problemId)
    {
        problemset::$maxAccepted = max(problemset::$maxAccepted, problemset::$problems[$problemId]->addUserSolved());
        problemset::update();
    }

    function addUserLiked($problemId)
    {
        problemset::$maxLike = max(problemset::$maxLike, problemset::$problems[$problemId]->addUserLiked());

        problemset::update();
    }

    static function chooseProblem($L, $R, $tags)
    {
        $sortOnBtr = array();
        foreach (problemset::$problems as $k => $v) {
            if ($L <= $v->calcDif() && $v->calcDif() <= $R && $v->used != true)
                $sortOnBtr[$k] = $v->calcBtr($tags, problemset::$maxAccepted, problemset::$maxLike);
        }
        $candid = array();
        for ($i = 0; $i < 3; $i++) {
            $str = "";
            foreach ($sortOnBtr as $k => $v) {
                if (strlen($str) == 0 || $v > $sortOnBtr[$str])
                    $str = $k;
            }
            if ($str != "") {
                $candid[$i] = $str;
            }
        }
        if (count($candid) == 0)
            return false;
        $ans = $candid[rand(0, count($candid) - 1)];
        problemset::$problems[$ans]->setUsed();
        problemset::update();
        return problemset::$problems[$ans]->problemName;
    }
}