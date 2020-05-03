<?php

echo file_get_contents("error.html");
if (!preg_match_all("/href=\"\/gym\/([0-9]+)\//", file_get_contents("error.html"), $matches)) {
        echo "defh";
}
echo "W";