<?php

class user
{
    public $username;
    public $warm, $scores = array();

    public function __construct($username, $warm = 0, $scores = array() )
    {
        $this->username = $username;
        $this->warm = $warm;
        $this->scores = $scores;
    }
    public function sleep(){
        array_push($this->scores, $this->warm);
        $this->warm*= 0.96;
    }
    public function  addRating($x){
        $this->warm+= $x;
    }
    public function getRating($today)
    {
        $ans = array();
        for($i = 0; $i < count($this->scores); $i++) {
            array_push($ans, array("x" => $today-count($this->scores) + $i + 1, "y" => $this->scores[$i]*8));
        }
        return $ans;
    }
}