<?php

class problem
{
    public $like = 0, $accepted = 0; // from iran dataset
    public $tags, $difficulty, $prior;
    public $used, $problemName;

    public function __construct($problemName, $tags, $difficulty, $prior = 0, $used = false)
    {
        $this->problemName = $problemName;
        $this->tags = $tags;
        $this->difficulty = $difficulty;
        $this->prior = $prior;
        $this->used = $used;
    }

    public function merge($tags, $difficulty, $prior = 0, $used = false)
    {
        $this->tags = array_unique(array_merge($this->tags, $tags));
        $this->difficulty = $difficulty;
        $this->prior = max($prior, $this->prior);
        $this->used = ($this->used || $used);
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

    function evalWithCoff($A, $B, $C, $D, $E)
    {
        return 7 * $B + 5 * $A + 4 * $B + 2 * $D + 2 * $E;
    }

    function calcBtr($tags, $maxAccepted, $maxLike, $L, $R)
    { // each item is 0.5 if empty
        if ($maxLike == 0)
            $maxLike = 1;
        if ($maxAccepted == 0)
            $maxAccepted = 1;
        $intersect = 0.5;
        if (count($tags) != 0) {
            $intersect = 0;
            foreach ($tags as $value) {
                if (in_array($value, $this->tags))
                    $intersect++;
            }
            $intersect = $intersect / count($tags);
        }
        $dif = $this->calcDif() - (($L + $R) / 2);
        $len2 = ($R - $L) / 2;
        return $this->evalWithCoff($intersect, $this->prior, $this->accepted / $maxAccepted, $this->like / $maxLike, 1 - ($dif * $dif) / ($len2 * $len2));
    }
}
