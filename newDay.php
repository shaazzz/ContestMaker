<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/Models/problemset.php';
require __DIR__ . '/data/defines.php';
require __DIR__ . '/CodeforcesUserApi.php';

problemset::readFromFile();

echo