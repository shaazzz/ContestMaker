<?php

require __DIR__ . '/problem.php';

class problemset
{
    static $problems = array();
    static $maxLike = 0, $maxAccepted = 0;

    static function takeBackup()
    {
        $dir = "data/dataBackups/";
        $array_map = array_map('filemtime', ($files = glob($dir . "data*.txt")));
        array_multisort($array_map, SORT_ASC, $files);
        if (file_exists("data/data.txt")) {
            $text = file_get_contents("data/data.txt");
            file_put_contents("data/dataBackups/data" . Date(date(" Y.m.d h:m:s")) . ".txt", $text);
            if (count($files) > MAXIMUM_BACKUP_FILES) {
                unlink($files[0]);
            }
        }
    }

    static function resetUsed()
    {
        foreach (problemset::$problems as $problem) {
            $problem->used = false;
        }
        problemset::update();
    }

    static function readFromFile()
    {
        problemset::takeBackup();
        if (file_exists("data/data.txt")) {
            $data = json_decode(file_get_contents("data/data.txt"), true);
            foreach ($data as $problemJson) {
                problemset::addProblem($problemJson["problemName"], $problemJson["tags"],
                    $problemJson["difficulty"], $problemJson["prior"], (bool)$problemJson["used"], true);
            }
        }
    }

    static function update()
    {
        file_put_contents("data/data.txt", json_encode(problemset::$problems));
    }

    static function addProblem($problemName, $tags, $difficulty, $prior, $used, $inside = false)
    {
        $problemId = preg_split('/ /', $problemName)[0];
        if (!isset(problemset::$problems[$problemId])) {
            problemset::$problems[$problemId] = new problem($problemId, $tags, $difficulty, $prior, $used, $inside);
        } else {
            problemset::$problems[$problemId]->merge($tags, $difficulty, $prior, $used, $inside);
        }
        if (!$inside) {
            problemset::update();
        }
        return $problemId;
    }

    static function addUserSolved($problemId, $inside = false)
    {
        problemset::$maxAccepted = max(problemset::$maxAccepted, problemset::$problems[$problemId]->addUserSolved());
        if (!$inside) {
            problemset::update();
        }
    }

    static function resetUserSolved()
    {
        problemset::$maxAccepted = 0;
        foreach (problemset::$problems as $problem) {
            $problem->accepted = 0;
        }
        problemset::update();
    }

    static function addUserLiked($username, $problemId, $inside = false)
    {
        problemset::$maxLike = max(problemset::$maxLike, problemset::$problems[$problemId]->addUserLiked($username));
        if (!$inside) {
            problemset::update();
        }
    }

    static function chooseProblem($L, $R, $tags)
    {
        $sortOnBtr = array();
        foreach (problemset::$problems as $k => $v) {
            if ($L <= $v->calcDif() && $v->calcDif() <= $R && $v->used != true)
                $sortOnBtr[$k] = $v->calcBtr($tags, problemset::$maxAccepted, problemset::$maxLike, $L, $R);
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
            } else {
                throw new Exception("field to build contest choose problem");
            }
        }
        if (count($candid) == 0)
            return false;
        $ans = $candid[rand(0, count($candid) - 1)];
        problemset::$problems[$ans]->used = true;
        problemset::update();
        return problemset::$problems[$ans]->problemName;
    }
}