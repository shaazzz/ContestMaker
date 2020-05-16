<?php

require_once __DIR__ . '/problem.php';
require_once __DIR__ . '/APIException.php';

class problemset
{
    static $problems = array();

    static function takeBackup()
    {
        $dir = "data/dataBackups/";
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
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
        if (file_exists(realpath("data/data.txt"))) {
            $data = json_decode(file_get_contents("data/data.txt"), true);
            foreach ($data as $problemJson) {
                problemset::addProblem($problemJson["problemName"], $problemJson["tags"],
                    $problemJson["difficulty"], $problemJson["prior"], (bool)$problemJson["used"],
                    (int)$problemJson["like"], (int)$problemJson["accepted"], $problemJson["usersLike"], true);
            }
        }
    }

    static function update()
    {
        file_put_contents("data/data.txt", json_encode(problemset::$problems));
    }

    static function addProblem($problemName, $tags, $difficulty, $prior, $used, $like = 0, $accepted = 0, $usersLike = null, $inside = false)
    {
        $problemId = preg_split('/ /', $problemName)[0];
        if (!isset(problemset::$problems[$problemId])) {
            problemset::$problems[$problemId] = new problem($problemId, $tags, $difficulty, $prior, $used, $like, $accepted, $usersLike, $inside);
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
        problemset::$problems[$problemId]->addUserSolved();
        if (!$inside) {
            problemset::update();
        }
    }

    static function resetUserSolved()
    {
        foreach (problemset::$problems as $problem) {
            $problem->accepted = 0;
        }
        problemset::update();
    }

    static function addUserLiked($username, $problemId, $inside = false)
    {
        if (!isset(problemset::$problems[$problemId])) {
            throw new APIException("problem doesn't exist!");
        }
        problemset::$problems[$problemId]->addUserLiked($username);
        if (!$inside) {
            problemset::update();
        }
    }

    static function chooseProblem($L, $R, $tags, array $forbiddenProblemIds)
    {
        $maxLike = array();
        $maxAccepted = array();

        foreach (problemset::$problems as $k => $v) {
            if (!isset($maxLike[$v->difficulty]) || !isset($maxAccepted[$v->difficulty])) {
                $maxLike[$v->difficulty] = 0;
                $maxAccepted[$v->difficulty] = 0;
            }
            $maxLike[$v->difficulty] = max($maxLike[$v->difficulty], $v->like);
            $maxAccepted[$v->difficulty] = max($maxAccepted[$v->difficulty], $v->accepted);
        }

        $sortOnBtr = array();
        foreach (problemset::$problems as $k => $v) {
            if ($L <= $v->calcDif() && $v->calcDif() <= $R && $v->used != true && !in_array($v->problemName, $forbiddenProblemIds)) {
                $sortOnBtr[$k] = $v->calcBtr($tags, $maxAccepted[$v->difficulty], $maxLike[$v->difficulty], $L, $R);
            }
        }
        $candid = array();
        for ($i = 0; $i < 3; $i++) {
            $str = "";
            foreach ($sortOnBtr as $k => $v) {
                if ((strlen($str) == 0 || $v > $sortOnBtr[$str]) and !in_array($str, $candid))
                    $str = $k;
            }
            if ($str != "") {
                $candid[$i] = $str;
            } else {
                throw new APIException("field to build contest choose problem");
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