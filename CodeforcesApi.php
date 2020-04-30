<?php

function generateRandomString($length = 6)
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
            $randomString = generateRandomString();
            $data = $randomString . "/" . $methodName . "?" . http_build_query($parameters) . "#" . $this->apiSecrets[$user];
            $parameters['apiSig'] = $randomString . hash('sha512', $data);
        }
        $query = self::$url . $methodName . "?" . http_build_query($parameters);
        return json_decode(file_get_contents($query), true);
    }
}