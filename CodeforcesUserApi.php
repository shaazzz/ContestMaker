<?php
require __DIR__ . "/pdfcrowd.php";

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
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, 'data/cookies.txt');
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, "data/cookies.txt");
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        curl_setopt($this->curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_MAXREDIRS, 20);
        $this->ftaaCode = generateRandomString(18);
        $this->bfaaCode = "f1b3f18c715565b589b7823cda7448ce";
        $this->findCsrf();
    }

    function findCsrf()
    {
        $body = curl_exec($this->curl);
        if (!preg_match("/csrf='(.+?)'/", $body, $match)) {
            throw new Exception("cf token not found");
        }
        $this->csrf_token = $match[1];
    }

    function deleteCookies()
    {
        $this->closeConnection();
        unlink("data/cookies.txt");
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

    function request($actionAddress, $parameters, $returnEveryThing = false)
    {
        curl_setopt($this->curl, CURLOPT_URL, self::$url . $actionAddress);
        curl_setopt($this->curl, CURLOPT_POST, true);
        $parameters = array_merge($this->getAdditionalParameters(), $parameters);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($parameters));
        $result = curl_exec($this->curl);
        if ($returnEveryThing) {
            return $result;
        }
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
        $this->findCsrf();
    }

    function getValueFromBody($body, $name)
    {
        if (!preg_match("/name=\"$name\" value=\"([\s\S]+?)\"/", $body, $match)) {
            throw new Exception("can't find " . $name);
        }
        return $match[1];
    }

    function getPlaceholderFromBody($body, $name)
    {
        if (!preg_match("/name=\"$name\" value=\"\" placeholder=\"([\s\S]+?)\"/", $body, $match)) {
            return $this->getValueFromBody($body, $name);
        }
        return $match[1];
    }

    function setNewProblemsForContest($contestId, $problemIds, $contestAddressPrefix = "gym")
    {
        $this->setVisibilityProblems($contestId, false, $contestAddressPrefix);
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
        $contestName = $this->getValueFromBody($body, "contestName");
        $contestDuration = $this->getValueFromBody($body, "contestDuration");
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
        echo $result;
    }

    function getContestProblemLinks($contestId, $contestAddressPrefix = "gym")
    {
        $body = $this->request($contestAddressPrefix . "/" . $contestId, array());
        if (!preg_match_all("/$contestAddressPrefix\/$contestId\/problems\/edit\/.+\"/", $body, $matches)) {
            throw new Exception("cannot find contest problem ids");
        }
        $result = array();
        foreach ($matches[0] as $match) {
            $link = substr($match, 0, strlen($match) - 1);
            array_push($result, $link);
        }
        return $result;
    }

    function getContestProblemQueries($contestId, $contestAddressPrefix = "gym")
    {
        $result = array();
        $links = $this->getContestProblemLinks($contestId, $contestAddressPrefix);
        foreach ($links as $link) {
            $body = $this->request($link, array());
            $problemQuery = $this->getValueFromBody($body, "problemQuery");
            array_push($result, $problemQuery);
        }
        return $result;
    }


    function setVisibilityProblems($contestId, $visibility, $contestAddressPrefix = "gym")
    {
        $links = $this->getContestProblemLinks($contestId, $contestAddressPrefix);
        foreach ($links as $link) {
            $body = $this->request($link, array());
            $this->request($link, array(
                "action" => "change",
                "index" => $this->getValueFromBody($body, "index"),
                "problemQuery" => $this->getValueFromBody($body, "problemQuery"),
                "timeLimit" => $this->getPlaceholderFromBody($body, "timeLimit"),
                "memoryLimit" => $this->getPlaceholderFromBody($body, "memoryLimit"),
                "hidden" => ($visibility ? "off" : "on")
            ));
        }
    }

    function sendScoreboard()
    {
        $data = array('url' => self::$url . "contest/1278/standings");//"group/tFEA7pkTiD/contest/272297/standings");

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://hcti.io/v1/image");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "0b429c7a-a620-44bc-aff8-773fa0f3e14d" . ":" . "059cce1c-d69d-4ff6-bb0c-739c4ce468b7");

        $headers = array();
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $res = json_decode($result, true);

        $im = imagecreatefrompng($res['url']);
        $im2 = imagecrop($im, ['x' => 50, 'y' => 180, 'width' => imagesx($im) - 100, 'height' => imagesy($im) - 230]);
        if ($im2 !== FALSE) {
            imagepng($im2, 'example-cropped.png');
            imagedestroy($im2);
        }
        imagedestroy($im);
        $this->sendPhoto('example-cropped.png');
    }

    function sendPhoto($filename)
    {
        $data = array(
            "chat_id" => TELEGRAM_CHANNEL_ID,
            "caption" => TELEGRAM_SCOREBOARD_CAPTION,
            "photo" => curl_file_create(realpath($filename), 'image/png', "example-cropped.png")
        );
        $proxyIP = '127.0.0.1';
        $proxyPort = '41177';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_PROXY, $proxyIP);
        curl_setopt($ch, CURLOPT_PROXYPORT, $proxyPort);
        curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot" . TELEGRAM_API . "/sendPhoto");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if (json_decode(curl_exec($ch), true)['ok'] != true || curl_error($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);
    }

    function closeConnection()
    {
        curl_close($this->curl);
    }
}

