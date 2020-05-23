<?php

$request = strtolower($_SERVER['REQUEST_URI']);

$types = array(
    array("pattern" => "/^\/ranking|\/$/", "address" => "views/ranking.php"),
    array("pattern" => "/^\/profile\/(.+)$/", "address" => "views/report.php"),
    array("pattern" => "/^\/suggestproblems\/(.+)$/", "address" => "views/suggestProblems.php"),
    array("pattern" => "/^.+$/", "address" => "views/404.php"),
);

chdir("..");
require_once 'data/defines.php';

foreach ($types as $tp) {
    if (preg_match($tp['pattern'], $request, $matches)) {
        $_GET['input'] = $matches[1];
        require_once $tp['address'];
        exit(0);
    }
}