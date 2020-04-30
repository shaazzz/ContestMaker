<?php

require __DIR__ . '/problem.php';

class problemset{
    private $problems = array();
    public $maxLike = 0, $maxAccepted = 0;

    function start(){
        // somehow readFiles
        foreach($problems as $p){
            $p->restart();
        }
    }
    function finish(){
        // somehow writeFiles
    }
    function addProblem($problemId, $tags, $difficulty, $prior = 0){
        if($this->problems[$problemId] == null){
            $this->problems[$problemId] = new problem($tags, $difficulty, $prior);
        }
    }
    function deleteProblem($problemId){

    }
}