<?php


class AllContests
{
    static $contests;

    static function takeBackup()
    {
        $dir = "data/dataBackups/";
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $array_map = array_map('filemtime', ($files = glob($dir . "contest*.txt")));
        array_multisort($array_map, SORT_ASC, $files);
        if (file_exists("data/contest.txt")) {
            $text = file_get_contents("data/contest.txt");
            file_put_contents("data/dataBackups/contest" . Date(date(" Y.m.d h:m:s")) . ".txt", $text);
            if (count($files) > MAXIMUM_BACKUP_FILES) {
                unlink($files[0]);
            }
        }
    }

    static function readFromFile()
    {
        AllContests::takeBackup();
        if (file_exists("data/contest.txt")) {
            $settings = json_decode(file_get_contents("data/weekContestSettings.txt"), true);
            $data = json_decode(file_get_contents("data/contest.txt"), true);
            foreach ($data as $contestIndex => $contestJsonArray) {
                foreach ($contestJsonArray as $contestLevel => $contestId) {
		    $setting = $settings["Week" . $contestIndex][$contestLevel];
                    AllContests::addContest($contestIndex, $contestLevel,
                        $setting["difficulties"], $setting["tags"], $setting["negativeTags"], $contestId, null, true);
                }
            }
        }
    }

    static function addContest($contestIndex, $contestLevel, $difficulties, $tags, $negativeTags, $contestId, $api = null, $inside = false)
    {
        $contestIndex = (int)$contestIndex;
        if (!isset(AllContests::$contests[$contestIndex][$contestLevel])) {
            $contest = new contest($api, $contestIndex, $contestLevel, $difficulties, $tags, $negativeTags, $contestId);
            AllContests::$contests[$contestIndex][$contestLevel] = $contest;
        }
        if (!$inside) {
            AllContests::update();
        }
    }

    static function update()
    {
        $contestIds=array();
        foreach (AllContests::$contests as $weekId=>$weekContests){
            foreach ($weekContests as $key=>$contest) {
                $contestIds[$weekId][$key]=$contest->contestId;
            }
        }
        file_put_contents("data/contest.txt", json_encode($contestIds));
    }
}
