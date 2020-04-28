<?php


class Problem
{
    private $contestId;
    private $index;
    private $score;
    private $rating;
    private $usersSolved = array();

    function __construct($contestId, $index, $score, $rating)
    {
        $this->contestId = $contestId;
        $this->index = $index;
        $this->score = $score;
        $this->rating = $rating;
    }

    function hasSolved($user){

    }

    function getId()
    {
        return $this->contestId . $this->index;
    }

    function addUserSolved($user)
    {
        array_push($this->usersSolved, $user);
    }
}