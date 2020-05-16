<?php

function exception_handler($exception)
{
    echo "error!\n";
    $dir = "data/errors/";
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    $message = date("M/d/Y h:m:s") . ": " . gettype($exception) . " on '" . $exception->getFile() . "' Line " . $exception->getLine() . " : " . $exception->getMessage();
    file_put_contents($dir . "errorMessages.txt", file_get_contents($dir . "errorMessages.txt") . "\n" . $message);

}

set_exception_handler("exception_handler");

// codeforces default username
define("CODEFORCES_USERNAME", "...");
// codeforces default password
define("CODEFORCES_PASSWORD", "...");

// codeforces api key
define("CODEFORCES_API_KEY", "...");
// codeforces api secret
define("CODEFORCES_API_SECRET", "...");

define("TELEGRAM_SCOREBOARD_CAPTION", "نفرات اول این هفته🥳");
define("CF_GROUP_PREFIX_ADDRESS", "group/" . CF_GROUP_ID . "/contest");
define("MAXIMUM_BACKUP_FILES", 10);
define("TIMER_UPDATE_EVERY_DAY", true);
// codeforces group token
define("CF_GROUP_ID", "...");

// these are just for telegram api (optional, for sending scoreboard to telegram channel)
define("PROXY_IP", "...");
define("PROXY_PORT", "...");

// telegram api token (optional, for sending scoreboard to telegram channel)
define("TELEGRAM_API", "...");
// telegram channel id (optional, for sending scoreboard to telegram channel)
define("TELEGRAM_CHANNEL_ID", "@...");
// https://hcti.io token (optional, for sending scoreboard to telegram channel)
define("IMG_PWD", "..." . ":" . "...");