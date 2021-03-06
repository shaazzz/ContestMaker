<?php

class contest
{
    public $contestId;
    private $difficulties, $tags, $negativeTags, $contestIndex, $contestLevel, $workingDays;

    public function __construct($api, $contestIndex, $contestLevel, $difficulties, $workingDays, $tags = null, $negativeTags = null, $contestId = null)
    {
        $this->workingDays=$workingDays;
        $this->contestIndex = $contestIndex;
        $this->contestLevel = $contestLevel;
        $this->difficulties = $difficulties;
        $this->contestId = $contestId;
        if (!isset($tags)) {
            $this->tags = array();
        } else {
            $this->tags = $tags;
        }
        if (!isset($negativeTags)) {
            $this->negativeTags = array();
        } else {
            $this->negativeTags = $negativeTags;
        }
        if (!isset($contestId)) {
            $this->contestId = $api->createNewMashup($contestIndex, $contestLevel);
            $api->changeTimeToToday($this);
            $api->addContestToGroup($this->contestId);
        }
    }

    public function getDifficulties()
    {
        return $this->difficulties;
    }

    /**
     * @return mixed
     */
    public function getContestIndex()
    {
        return $this->contestIndex;
    }

    /**
     * @return mixed
     */
    public function getContestLevel()
    {
        return $this->contestLevel;
    }

    public function isWorking($dayNumber)
    {
        return $this->workingDays[$dayNumber % 7] == '1';
    }

    function giveContest($forbiddenProblemIds)
    {
        $ans = array();
        for ($i = 0; $i < count($this->difficulties); $i++) {
            $x = problemset::chooseProblem($this->difficulties[$i]['L'], $this->difficulties[$i]['R'], $this->tags, $this->negativeTags, $forbiddenProblemIds);
            if ($x == false)
                throw new Exception("error in building contest");
            $ans[$i] = $x;
        }
        return $ans;
    }
}