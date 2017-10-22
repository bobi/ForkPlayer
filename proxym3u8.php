<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 04.03.2017
 * Time: 22:48
 */

require_once('getallheaders.php');

$url = substr($_SERVER['REQUEST_URI'], strlen('/proxym3u8'));

//just for debugging on windows only
if (strcasecmp(substr($url, 0, strlen('http//')), 'http//') === 0) {
    $url = 'http://' . substr($url, strlen('http//'));
}

if (substr($url, 0, 1) == 'B') {
    if (strpos($url,"endbase64") !== false) {
        $url = base64_decode(substr($url, 1, strpos($url, 'endbase64') - 1)) . substr($url, strpos($url, 'endbase64') + 9);
    } else {
        $url = base64_decode(substr($url, 1, strlen($url) - 2));
    }
}

$ts = '';
$responseHeaders = [];
$userType = false;
$contentType = 'Content-type: application/vnd.apple.mpegurl';

if (strpos($url, "OPT:") !== false) {
    if (strpos($url, 'OPEND:/') == strlen($url) - 7) {
        $url = str_replace('OPEND:/', '', $url);
    } else {
        $ts = substr($url, strpos($url, 'OPEND:/') + 7);
    }

    if (strpos($url, 'OPEND:/') !== false) {
        $url = substr($url, 0, strpos($url, 'OPEND:/'));
    }

    $urlHeaders = explode('|', str_replace('--', '|', substr($url, strpos($url, 'OPT:') + 4)));

    $url = substr($url, 0, strpos($url, 'OPT:'));

    $requestHeaders = getallheaders();

    for ($i = 0; $i < count($urlHeaders); $i++) {
        $headerName = $urlHeaders[$i];
        $headerValue = $urlHeaders[++$i];

        if (strcasecmp('ContentType', $headerName) === 0) {
            if (!empty($requestHeaders['Range'])) {
                array_push($responseHeaders, 'Range' . ':' . $requestHeaders['Range']);
            }

            array_push($responseHeaders, 'Accept-Ranges' . ':' . 'bytes');

            $userType = true;
            $contentType = $headerName . ':' . $headerValue;
        } else {
            if (strcasecmp('Cookie', $headerName) === 0) {
                $headerValue = str_replace(';', ',', $headerValue);
            }

            array_push($responseHeaders, $headerName . ':' . $headerValue);
        }
    }
}

if (!$userType) {
    if (!empty($ts)) {
        $url = substr($url, 0, strripos($url, '/') + 1) . $ts;

        $contentType = 'Content-type: video/mp2t';
    } else {
        $contentType = 'Content-type: application/vnd.apple.mpegurl';
    }
}

header($contentType);

$opts = array(
    'http' => array(
        'method' => 'GET',
        'header' => $responseHeaders
    )
);

$context = stream_context_create($opts);

$fp = fopen($url, 'rb', false, $context);

if ($fp) {
    fpassthru($fp);

    fclose($fp);
}
