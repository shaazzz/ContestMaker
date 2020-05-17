<?php


class AllUsers
{
    static $users, $settings;

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
        AllUsers::$settings = json_decode(file_get_contents("data/ratingSettings.txt"), true);
        AllContests::takeBackup();
        if (file_exists("data/users.txt")) {
            $data = json_decode(file_get_contents("data/users.txt"), true);
            foreach ($data as $contestJsonArray) {
                AllUsers::addUser($contestJsonArray["username"], $contestJsonArray["fullName"], null, true);
            }
        }
    }

    static function addUser($username, $fullName, $api = null, $inside = false)
    {
        if (!isset(AllUsers::$users[$username])) {
            $user = new user($username, $fullName);
            AllUsers::$users[$username] = $user;
        }
        if (!$inside) {
            AllUsers::update();
        }
    }

    static function update()
    {
        file_put_contents("data/users.txt", json_encode(AllUsers::$users));
    }
}

?>