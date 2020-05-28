<?php

function generateRandomNumber($length = 6)
{
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

class CodeforcesApi
{
    private static $url = 'https://codeforces.com/api/';
    private $apiKeys = array();
    private $apiSecrets = array();
    private $defaultUser;

    function addUser($name, $apiKey, $apiSecret, $setToDefault = true)
    {
        $this->apiKeys[$name] = $apiKey;
        $this->apiSecrets[$name] = $apiSecret;
        if ($setToDefault) {
            $this->defaultUser = $name;
        }
    }

    function request($methodName, $parameters, $user = null)
    {
        if (!isset($user) && isset($this->defaultUser)) {
            $user = $this->defaultUser;
        }
        if (isset($user)) {
            $parameters['apiKey'] = $this->apiKeys[$user];
            $parameters['time'] = time();
            ksort($parameters);
            $randomString = generateRandomNumber();
            $data = $randomString . "/" . $methodName . "?" . http_build_query($parameters) . "#" . $this->apiSecrets[$user];
            $parameters['apiSig'] = $randomString . hash('sha512', $data);
        }
        $query = self::$url . $methodName . "?" . http_build_query($parameters);
        $ansStr = file_get_contents($query);
        $answer = json_decode($ansStr, true);
        if ($answer['status'] != 'OK') {
            throw new APIException("return status is not ok!", $ansStr);
        }
        return $answer;
    }

    function getAcceptedProblemIds($person)
    {
        $problems = array();
        $submissions = $this->request("user.status", array("handle" => $person))['result'];
        foreach ($submissions as $sub) {
            if ($sub["verdict"] == "OK") {
                if (!isset($sub["problem"]["contestId"]) || !isset($sub["problem"]["index"])) {
                    continue;
                }
                $sub["id"] = $sub["problem"]["contestId"] . $sub["problem"]["index"];
                array_push($problems, $sub["id"]);
            }
        }
        return $problems;
    }

    function getForbiddenProblemIds($forbiddenUserIds)
    {
        $problems = array();
        foreach ($forbiddenUserIds as $forbiddenUserId) {
            $problems = array_merge($problems, $this->getAcceptedProblemIds($forbiddenUserId));
        }
        return array_unique($problems);
    }

    function getParticipatesActivities($contestId, $fromDay = 0, $showUnofficial = false)
    {
        $result = $this->request("contest.standings", array("contestId" => $contestId, "showUnofficial" => $showUnofficial))['result'];
        var_dump($result);
    }
}