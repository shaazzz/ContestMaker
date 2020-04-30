<?php

class problem
{
    public $like = 0, $accepted = 0; // from iran dataset
    public $tags, $difficulty, $prior;
    public $problemId, $used;

    public function __construct($problemId, $tags, $difficulty, $prior = 0)
    {
        $this->problemId = $problemId;
        $this->tags = $tags;
        $this->difficulty = $difficulty;
        $this->prior = $prior;
    }

    function setUsed()
    {
        $this->used = true;
    }

    function addUserSolved()
    {
        $this->accepted++;
        return $this->accepted;
    }

    function addUserLiked()
    {
        $this->like++;
        return $this->like;
    }

    function calcDif()
    {
        return $this->difficulty;
    }

    function evalWithCoff($A, $B, $C, $D)
    {
        return 7 * $B + 5 * $A + 4 * $B + 2 * $D;
    }

    function calcBtr($tags, $maxAccepted, $maxLike)
    { // each item is 0.5 if empty
        if ($maxLike == 0)
            $maxLike = 1;
        if ($maxAccepted == 0)
            $maxAccepted = 1;
        $intersect = 0.5;
        if (count($tags) != 0) {
            $intersect = 0;
            foreach ($tags as $value) {
                if ($this->tags[$value] != null)
                    $intersect++;
            }
            $intersect = $intersect / count($tags);
        }
        return $this->evalWithCoff($intersect, $this->prior, $this->accepted / $maxAccepted, $this->like / $maxLike);
    }
}
