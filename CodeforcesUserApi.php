<?php


function generateRandomString($length = 6)
{
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


class CodeforcesUserApi
{
    private static $url = 'https://codeforces.com/enter';
    private $curl;

    function login()
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, self::$url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, 'cookies.txt');
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, "cookies.txt");
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        curl_setopt($this->curl, CURLOPT_URL, self::$url);
        curl_setopt($this->curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_MAXREDIRS, 20);
        $body = curl_exec($this->curl);

        if (!preg_match("/csrf='(.+?)'/", $body, $match)) {
            throw new Exception("csrf not found");
        }

        $fields = array(
            "csrf_token" => $match[1],
            "action" => "enter",
            "ftaa" => generateRandomString(18),
            "bfaa" => "f1b3f18c715565b589b7823cda7448ce",
            "handleOrEmail" => "shaazzz_admin",
            "password" => "pass",
            "_tta" => "176",
            "remember" => "on"
        );
        $fields_string = http_build_query($fields);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $fields_string);


        $result = curl_exec($this->curl);
        $last = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);

        curl_close($this->curl);
    }
}

