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
    public function wake(){
        $maxR = 1500;
        $maxT = 100;
        $f = $this->warm * $maxT / $maxR / $maxR;
        $this->warm *= (1-$f);
    }
    public function sleep(){
        array_push($this->scores, $this->warm);
    }
    public function  addRating($x){
        $this->warm+= $x;
    }

    public function getRate(){
        $rates = json_decode(file_get_contents("data/rateColors.txt"), true);
        foreach ($rates as $rate) {
            if ($this->warm < $rate['endValue']) {
                return $rate;
            }
        }
        return null;
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