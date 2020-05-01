<?php


class AllContests
{
    static function takeBackup()
    {
        $dir = "data/dataBackups/";
        $array_map = array_map('filemtime', ($files = glob($dir . "data*.txt")));
        array_multisort($array_map, SORT_ASC, $files);
        $text = file_get_contents("data/data.txt");
        file_put_contents("data/dataBackups/data" . Date(date(" Y.m.d h:m:s")) . ".txt", $text);
        if (count($files) > MAXIMUM_BACKUP_FILES) {
            unlink($files[0]);
        }
    }

    static function readFromFile()
    {
        problemset::takeBackup();
        if (file_exists("data/data.txt")) {
            $data = json_decode(file_get_contents("data/data.txt"), true);
            foreach ($data as $problemJson) {
                problemset::addProblem($problemJson["problemId"], $problemJson["problemName"], $problemJson["tags"],
                    $problemJson["difficulty"], $problemJson["prior"], $problemJson["used"], true);
            }
        }
    }

    static function addContest($contestIndex, $contestLevel){

    }

    static function update()
    {
        file_put_contents("data/data.txt", json_encode(problemset::$problems));
    }
}