<?php


class CodeforcesUserApi
{
    function login()
    {

        //'addMashupForm'
        $url = 'https://codeforces.com/data/mashup';
        $fields = array(
            "action" => "getProblem",
            "problemQuery" => "233E",
            "problemCount" => 1,
        );

        $fields_string = "";
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');

        /* STEP 1. let’s create a cookie file */
        $ua = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_COOKIESESSION, true );
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies.txt");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 20);
        curl_setopt($ch, CURLOPT_COOKIE, file_get_contents('cookies.txt'));
        //curl_setopt($ch, CURLOPT_POST, true);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);


        $result = curl_exec($ch);
        $last = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        echo $result;
        curl_close($ch);
        return array($result,$last);
    }
}

