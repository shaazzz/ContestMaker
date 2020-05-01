<?php

require __DIR__ . '/problemset.php';

class contest
{
    public $contestId, $L, $R, $tags, $cntProblems, $contestIndex, $contestLevel;

    public function __construct($api, $contestIndex, $contestLevel, $L, $R, $cntProblems, $tags = null, $contestId=null)
    {
        $this->contestIndex = $contestIndex;
        $this->contestLevel = $contestLevel;
        $this->L = $L;
        $this->R = $R;
        $this->cntProblems = $cntProblems;
        $this->contestId = $contestId;
        if (!isset($tags)) {
            $this->tags = array();
        } else {
            $this->tags = $tags;
        }
        if(!isset($contestId)) {
            $this->contestId = $api->createNewMashup($contestIndex, $contestLevel);
            $api->changeTimeToToday($this);
            $api->addContestToGroup($this->contestId);
        }
    }

    function giveContest()
    {
        // age nashe false mide
        $ans = array();
        for ($i = 0; $i < $this->cntProblems; $i++) {
            $x = problemset::chooseProblem($this->L, $this->R, $this->tags);
            if ($x == false)
                throw new Exception("error in building contest");
            $ans[$i] = $x;
        }
        return $ans;
    }
}