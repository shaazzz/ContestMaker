<?php

class problem{
    private $like = 0, $accepted = 0; // from iran dataset
    private $tags = array(), $difficulty, $prior;
    private $problemIndex, $contestId, $problemId;
    public function __construct($problemIndex, $contestId, $tags, $difficulty, $prior = 0){
        $this->problemIndex = $problemIndex;
        $this->contestId = $contestId;
        $this->problemId = $this->contestId . $this->problemIndex;
        $this->tags = $tags;
        $this->difficulty = $difficulty;
        $this->prior = $prior;
    }
    function addUserSolved(){
        $this->accepted++;
        return $this->accepted;
    }
    function addUserLiked(){
        $this->like++;
        return $this->like;
    }
    function calcDif(){
        return $this->difficulty;
    }
    function calcBtr($tags, $maxAccepted, $maxLike){ // each item is 0.5 if empty
        if($maxLike == 0)
            $maxLike = 1;
        if($maxAccepted == 0)
            $maxAccepted = 1;
        $intersect = 0.5;
        if(count($tags) != 0){
            $intersect = 0;
            foreach($tags as $value){
                if($this->tags[$value] != null)
                    $intersect++;
            }
            $intersect = $intersect / count($tags);
        }
        return evalWithCoff($intersect, $this->prior, $this->accepted / $maxAccepted, $this->like / $maxLike);
    }
}
