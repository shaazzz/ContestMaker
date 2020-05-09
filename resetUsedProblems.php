<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/data/defines.php';
require __DIR__ . '/Models/CodeforcesApi.php';
require __DIR__ . '/Models/problemset.php';

problemset::readFromFile();
problemset::resetUsed();

?>