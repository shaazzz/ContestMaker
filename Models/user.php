<?php

class user
{
    public $username, $fullName;
    public $warm, $scores = array();

    public function __construct($username, $fullName)
    {
        $this->username = $username;
        $this->fullName = $fullName;
        $this->warm = 0;
    }
    public function sleep(){
        array_push($this->scores, $this->warm);
        $this->warm*= 0.9;
    }
    public function  addRating($x){
        $this->warm+= $x;
    }
    public function getRating($today)
    {
        $ans = array();
        for($i = 0; $i < count($this->scores); $i++) {
            array_push($ans, array("x" => $today-count($this->scores) + $i + 1, "y" => $this->scores[$i]));
        }
        return $ans;
    }
}