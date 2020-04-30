<?php

require __DIR__ . '/problem.php';

class problemset{
    private $problems = array(), $used = array();
    private $maxLike = 0, $maxAccepted = 0;

    public function __construct(){
        $txt = file_get_contents("data.txt");
        $arr = explode(",", $txt);
        foreach($arr as $id){
            $used[$id] = true;
        }
    }
    function finish(){
        $txt = "";
        foreach($this->used as $id){
            $txt.=$id.",";
        }
        file_put_contents("data.txt", $txt);
    }
    function addProblem($problemIndex, $contestId, $tags, $difficulty, $prior = 0){
        $problemId = $contestId . $problemIndex;
        if($this->used[$problemId] != true){
            $this->problems[$problemId] = new problem($problemIndex, $contestId, $tags, $difficulty, $prior);
        }
    }
    function addUserSolved($problemId){
        $this->maxAccepted = max($this->maxAccepted, $this->problems[$problemId]->addUserSolved());
    }
    function addUserLiked($problemId){
        $this->maxLike = max($this->maxLike, $this->problems[$problemId]->addUserLiked());
    }
    function chooseProblem($tags, $L, $R){ // age natoonest false mide
        $sortOnBtr = array();
        foreach($this->problems as $k => $v){
            if($L <= $v->calcDif && $v->calcDif <= $R)
                $sortOnBtr[$k] = $v->calcBtr($tags, $this->maxAccepted, $this->maxLike);
        }
        $candid = array();
        for($i=0;$i<3;$i++){
            $str = "";
            foreach($sortOnBtr as $k => $v){
                if($v > $sortOnBtr[$str])
                    $str = $k;
            }
            if($str != ""){
                $candid[$i] = $str;
            }
        }
        if(count($candid) == 0)
            return false;
        $ans = $candid[rand(0, count($candid)-1)];
        $used[$ans] = true;
        return $ans;
    }
}