<?php

require __DIR__ . '/data/defines.php';
require __DIR__ . "/Models/problemset.php";

$doc = new DOMDocument();
$doc->loadHTML(file_get_contents("data/fav.html"));


$tables = $doc->getElementsByTagName('table');
$finder = new DomXPath($doc);
$classname = "problems";
$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
$finder = new DomXPath($doc);
$problemIds = array();

$classname = "id left";
$problemsTd = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
foreach ($problemsTd as $problemTd) {
    array_push($problemIds, trim($problemTd->nodeValue));
}
$classname = "id dark left";
$problemsTd = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
foreach ($problemsTd as $problemTd) {
    array_push($problemIds, trim($problemTd->nodeValue));
}
$problemIds = array_unique($problemIds);

$allProblemsArray = json_decode(file_get_contents("https://codeforces.com/api/problemset.problems"), true)['result'];
$allProblems = array();
foreach ($allProblemsArray['problems'] as $problem) {
    $allProblems[$problem['contestId'] . $problem['index']] = $problem;
}
foreach ($allProblemsArray['problemStatistics'] as $problem) {
    $allProblems[$problem['contestId'] . $problem['index']]['solvedCount']['solvedCount'] = $problem;
}

problemset::readFromFile();
echo "all problems downloaded\n";
echo "processing likes...\n";
$newProblemsCount = 0;
$likedProblemsCount = 0;
foreach ($problemIds as $problemId) {
    if (!isset(problemset::$problems[$problemId])) {
        if (!isset($allProblems[$problemId]) || !isset($allProblems[$problemId]["tags"]) || !isset($allProblems[$problemId]["rating"])) {
            echo isset($allProblems[$problemId]["rating"])." ".isset($allProblems[$problemId]["tags"])." ".isset($allProblems[$problemId])."\n";
            continue;
        }
        $problem = $allProblems[$problemId];
        $newProblemsCount++;
        problemset::addProblem(
            $problemId,
            $problem["tags"],
            $problem["rating"],
            0, false, 0, 0, null, true);
    }
    $likedProblemsCount++;
    problemset::addUserLiked(strtolower('shayan.p'), $problemId, true);
}
echo "$newProblemsCount new problems added to problemset\n";
echo $likedProblemsCount . " problems liked\n";
problemset::update();
?>