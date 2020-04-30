<?php

class problem{
    private $like, $accepted; // from iran dataset
    private $tags = array(), $difficulty, $prior;
    private $used = false;

    function __construct($tags, $difficulty, $prior = 0){
        $this->tags = $tags;
        $this->difficulty = $difficulty;
        $this->prior = $prior;
    }
    function restart(){
        $this->like = 0;
        $this->accepted = 0;
    }
    function addUserSolved(){
        $this->accepted++;
    }
    function addUserLiked(){
        $this->like++;
    }
    function usedInContest(){
        $this->used = true;
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
