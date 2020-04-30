<?php

require __DIR__ . '/problemset.php';

class contest{
    private $L, $R, $tags, $cntProblems;

    public function __construct($L, $R, $tags, $cntProblems){
        $this->L = $L;
        $this->R = $R;
        $this->tags = $tags;
        $this->cntProblems = $cntProblems;
    }
    function giveContest(){ // age nashe false mide
        $ans = array();
        for($i = 0; $i < $this->cntProblems; $i++){
            $x = problemset::chooseProblem($this->L, $this->R, $this->tags);
            if($x == false)
                return false;
            $ans[$i] = $x;
        }
        return $ans;
    }
}