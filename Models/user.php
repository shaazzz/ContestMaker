<?php

class user
{
    public $username, $fullName;

    public function __construct($username, $fullName)
    {
        $this->username = $username;
        $this->fullName = $fullName;
    }

    function getRating($api){
        $dayNumber = (int)file_get_contents("data/counter.txt");
        $weekIndex = 1;
        $scores=array();
        foreach (AllContests::$contests as $week) {
            if ($weekIndex * 7 + 1 > $dayNumber) {
                break;
            }
            foreach ($week as $contest) {
                $scoreboard = $api->getScoreboard($contest->contestId, CF_GROUP_ID);
                if (isset($scoreboard[$this->username])) {
                    //AllUsers::$settings[""];
                }
            }
        }
    }
}