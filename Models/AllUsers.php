<?php

class AllUsers
{
    public static $users = array(), $settings;

    static function takeBackup()
    {
        $dir = "data/dataBackups/";
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $array_map = array_map('filemtime', ($files = glob($dir . "user*.txt")));
        array_multisort($array_map, SORT_ASC, $files);
        if (file_exists("data/user.txt")) {
            $text = file_get_contents("data/user.txt");
            file_put_contents("data/dataBackups/user" . Date(date(" Y.m.d h:m:s")) . ".txt", $text);
            if (count($files) > MAXIMUM_BACKUP_FILES) {
                unlink($files[0]);
            }
        }
    }

    static function readFromFile()
    {
        $dir = "data/users.txt";
        if (file_exists($dir)) {
            $data = json_decode(file_get_contents($dir), true);
            foreach ($data as $Array) {
                AllUsers::addUser($Array["username"], $Array["warm"], $Array["scores"], true);
            }
        }
    }

    static function addUser($username, $warm, $scores, $inside = false)
    {
        if (!isset(AllUsers::$users[$username])) {
            $user = new user($username, $warm, $scores);
            AllUsers::$users[strtolower($username)] = $user;
        }
        if (!$inside) {
            AllUsers::update();
        }
    }

    static function update()
    {
        file_put_contents("data/users.txt", json_encode(AllUsers::$users)); // dir problem
    }

    static function updateRatings($scoreboard, $contestCof, $L, $R){
        $arr = array();
        $oneAcc = array();
        foreach($scoreboard as $username => $solved){
            if(!isset(AllUsers::$users[$username])){
                AllUsers::$users[$username] = new user($username);
            }
            for($i = $L; $i < $R; $i++){
                if(!isset($arr[$i])){
                    $arr[$i] = 0;
                }
                if($solved[$i] == true && !isset($oneAcc[$username])){
                    $oneAcc[$username] = true;
                }
                if($solved[$i] == true){
                    $arr[$i]++;
                }
            }
        }
        foreach($scoreboard as $username => $solved){
            for($i = $L; $i < $R; $i++){
                if($solved[$i] == true){
                    $s = $contestCof * max(1/3, 1 - ($arr[$i] / count($oneAcc))); // change?
                    AllUsers::$users[$username]->addRating($s);
                }
            }
        }
        AllUsers::update();
    }
    static function startOftheDay(){
        foreach(AllUsers::$users as $user){
            $user->wake();
        }
        AllUsers::update();
    }
    static function endOftheDay(){
        foreach(AllUsers::$users as $user){
            $user->sleep();
        }
        AllUsers::update();
    }
}
?>