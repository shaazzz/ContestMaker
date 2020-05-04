<?php

$doc = new DOMDocument();
$doc->loadHTML(file_get_contents("data/fav.html"));


$tables = $doc->getElementsByTagName('table');
$finder = new DomXPath($doc);
$classname = "problems";
$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
$finder = new DomXPath($doc);
$problems = array();

$classname = "id left";
$problemsTd = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
foreach ($problemsTd as $problemTd) {
    array_push($problems, trim($problemTd->nodeValue));
}
echo $problems[count($problems) - 1];
$classname = "id dark left";
$problemsTd = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
foreach ($problemsTd as $problemTd) {
    array_push($problems, trim($problemTd->nodeValue));
}
$problems = array_unique($problems);

foreach ($problems as $problem) {
    problemset::addUserLiked(strtolower('shayan.p'), $problem);
}
?>