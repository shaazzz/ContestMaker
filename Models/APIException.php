<?php

class APIException extends Exception
{
    private $description;

    function __construct($message, $description = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->description = $description;
        $dir = "data/errors/";
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($dir . "errorMessages.txt", file_get_contents($dir . "errors.txt") . "\n" . date("M/d/Y h:m:s") . ": " . $this->getMessage());
        if (strlen($description) > 0) {
            file_put_contents($dir . "error " . date("M/d/Y h:m:s") . ".txt", $this->description);
        }
    }
}