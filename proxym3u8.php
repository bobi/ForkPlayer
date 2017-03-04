<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 04.03.2017
 * Time: 22:48
 */

$url = substr($_SERVER['REQUEST_URI'], strlen('/proxym3u8'));

//just for debugging on windows only
if (strcasecmp(substr($url, 0, strlen('http//')), 'http//') === 0) {
    $url = 'http://' . substr($url, strlen('http//'));
}

$ts = '';
$headers = array();

if (strpos($url, "OPT:") !== false) {
    if (strpos($url, 'OPEND:/') == strlen($url) - 7) {
        $url = str_replace('OPEND:/', '', $url);
    } else {
        $ts = substr($url, strpos($url, 'OPEND:/') + 7);
    }

    if (strpos($url, 'OPEND:/') !== false) {
        $url = substr($url, 0, strpos($url, 'OPEND:/'));
    }

    $requestHeaders = explode('|', str_replace('--', '|', substr($url, strpos($url, 'OPT:') + 4)));

    $url = substr($url, 0, strpos($url, 'OPT:'));

    for ($i = 0; $i < count($requestHeaders); $i++) {
        $headerName = $requestHeaders[$i];
        $headerValue = $requestHeaders[++$i];

        if (strcasecmp('Cookie', $headerName) === 0) {
            $headerValue = str_replace(';', ',', $headerValue);
        }

        array_push($headers, $headerName . ':'. $headerValue);
    }
}

if (!empty($ts)) {
    $url = substr($url, 0, strripos($url, '/') + 1) . $ts;

    header('Content-type: video/mp2t');
} else {
    header('Content-type: application/vnd.apple.mpegurl');
}

$opts = array(
    'http' => array(
        'method' => 'GET',
        'header' => $headers
    )
);

$context = stream_context_create($opts);

$fp = fopen($url, 'rb', false, $context);

if ($fp) {
    fpassthru($fp);

    fclose($fp);
}
