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
    private static $url = 'https://codeforces.com/';
    private $curl;
    private $csrf_token;
    private $ftaaCode;
    private $bfaaCode;

    function __construct()
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
        $this->ftaaCode = generateRandomString(18);
        $this->bfaaCode = "f1b3f18c715565b589b7823cda7448ce";
        if (!preg_match("/csrf='(.+?)'/", $body, $match)) {
            throw new Exception("cf token not found");
        }
        $this->csrf_token = $match[1];
    }

    function deleteCookies()
    {
        $this->closeConnection();
        unlink("cookies.txt");
        $this->__construct();
        echo $this->checkLogin();
    }

    function checkLoginHelper($body)
    {
        if (!preg_match("/handle = \"([\s\S]+?)\"/", $body)) {
            return false;
        }
        return true;
    }

    function checkLogin()
    {
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_URL, self::$url . 'enter');
        $result = curl_exec($this->curl);
        return $this->checkLoginHelper($result);
    }

    function getAdditionalParameters()
    {
        return array(
            "csrf_token" => $this->csrf_token,
            "ftaa" => $this->ftaaCode,
            "bfaa" => $this->bfaaCode,
            "_tta" => "176"
        );
    }

    function request($actionAddress, $parameters)
    {
        curl_setopt($this->curl, CURLOPT_URL, self::$url . $actionAddress);
        curl_setopt($this->curl, CURLOPT_POST, true);
        $parameters = array_merge($this->getAdditionalParameters(), $parameters);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($parameters));
        $result = curl_exec($this->curl);
        $header_size = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
        return substr($result, $header_size);
    }

    function login($username, $password)
    {
        $this->deleteCookies();
        $result = $this->request('enter', array(
            "action" => "enter",
            "handleOrEmail" => $username,
            "password" => $password,
            "remember" => "on"
        ));
        if ($this->checkLoginHelper($result)) {
            echo "login successful\n";
        } else {
            throw new Exception("login failed");
        }
    }

    function getContestName($body)
    {
        if (!preg_match("/name=\"contestName\" value=\"([\s\S]+?)\"/", $body, $match)) {
            throw new Exception("can't find contest name");
        }
        return $match[1];
    }

    function getContestDuration($body)
    {
        if (!preg_match("/name=\"contestDuration\" value=\"([\s\S]+?)\"/", $body, $match)) {
            throw new Exception("can't find contest name");
        }
        return $match[1];
    }

    function addProblemsToContest($contestId, $problemIds)
    {
        $problems = array();
        foreach ($problemIds as $problemId) {
            $result = $this->request('data/mashup', array(
                "action" => "getProblem",
                "problemQuery" => $problemId,
                "problemCount" => 0
            ));
            array_push($problems, json_decode($result));
        }
        $body = $this->request("gym/$contestId/problems/new", array());
        $contestName = $this->getContestName($body);
        $contestDuration = $this->getContestDuration($body);
        echo $contestName . $contestDuration;
        $problems = json_encode($problems);
        echo $problems;//
        $result = $this->request('data/mashup', array(
            "action" => "saveMashup",
            "isCloneContest" => "false",
            "parentContestIdAndName" => $contestId . ' - ' . $contestName,
            "parentContestId" => $contestId,
            "contestId" => $contestId,
            "contestName" => $contestName,
            "contestDuration" => $contestDuration,
            "problemsJson" => $problems
        ));
    }

    function closeConnection()
    {
        curl_close($this->curl);
    }
}

