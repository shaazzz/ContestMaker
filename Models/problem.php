<?php

class problem
{
    public $like, $accepted, $usersLike; // from iran dataset
    public $tags, $difficulty, $prior;
    public $used, $problemName;

    public function __construct($problemName, $tags, $difficulty, $prior = 0, $used = false, $like = 0, $accepted = 0, $usersLike = null, $inside = false)
    {
        if(!isset($usersLike)){
            $usersLike=array();
        }
        $this->like=$like;
        $this->accepted=$accepted;
        $this->usersLike=$usersLike;
        $this->problemName = $problemName;
        $this->tags = $tags;
        $this->difficulty = $difficulty;
        $this->prior = $prior;
        $this->used = $used;
        if (!$inside) {
            echo "<br> new problem ($problemName) added to problemset\n";
        }
    }

    public function merge($tags, $difficulty, $prior = 0, $used = false, $inside = false)
    {
        $this->tags = array_values(array_unique((array_merge($this->tags, $tags))));
        $this->difficulty = $difficulty;
        $this->prior = max($prior, $this->prior);
        $this->used = ($this->used || $used);
        if (!$inside) {
            echo "<br> problem ($this->problemName) merged with problemset\n";
        }
    }

    function addUserSolved()
    {
        $this->accepted++;
        return $this->accepted;
    }

    function changePrior($prior){
        $this->prior = max($prior, $this->prior);
    }

    function addUserLiked($username)
    {
        array_push($this->usersLike, $username);
        $this->usersLike = array_unique($this->usersLike);
        $this->like = count($this->usersLike);
        return $this->like;
    }

    function calcDif()
    {
        return $this->difficulty;
    }

    function evalWithCoff($A, $B, $C, $D, $E)
    {
        return 7 * $B + 5 * $A + 4 * $C + 2 * $D + $E; // E not deleted
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
