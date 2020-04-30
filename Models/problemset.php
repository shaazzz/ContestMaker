<?php

require __DIR__ . '/problem.php';
require __DIR__ . '/io.php';

class problemset{
    private $problems = array(), $used = array();
    private $maxLike = 0, $maxAccepted = 0;

    function __construct(){
        $txt = file_get_contents("data.txt");
        $arr = explode(",", $txt);
        foreach($arr as $id){
            $used[id] = true;
        }
    }
    function finish(){
        $txt = "";
        foreach($arr as $id){
            $txt.=$id.",";
        }
        file_put_contents("data.txt", $txt);
    }
    function addProblem($problemIndex, $contestId, $tags, $difficulty, $prior = 0){
        $problemId = $contestId . $problemIndex;
        if($this->used[$problemId] != true){
            $this->problems[$problemId] = new problem($tags, $difficulty, $prior);
        }
    }
    function addUserSolved($problemId){
        $this->maxAccepted = max($this->maxAccepted, $this->problems[$problemId]->addUserSolved());
    }
    function addUserLiked($problemId){
        $this->maxLike = max($this->maxLike, $this->problems[$problemId]->addUserLiked());
    }
    function chooseProblem($tags){

    }
}