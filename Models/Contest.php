<?php


class Contest
{
    private $problems;
    private $users = array();

    function __construct($problems)
    {
        $this->problems = $problems;
    }

    function getRating()
    {
        $scoreboard = array();
        foreach ($this->users as $user) {
            $user_score = array();
            foreach ($this->problems as $problem){
                if($problem->get)
            }
            array_push($scoreboard, $user_score);
        }
    }
}