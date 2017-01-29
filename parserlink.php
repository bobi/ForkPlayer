<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 26.01.2017
 * Time: 21:15
 */

/**
 * @param string $command
 * @return array
 */
function parse_curl_command($command)
{
    $curl_opts = array();
    if (preg_match("/(?:\")(.*?)(?=\")/s", $command, $matches) === 1) {
        $curl_opts[CURLOPT_URL] = $matches[1];

        if (preg_match_all('/(?:-H\s\")(.*?)(?=\")/s', $command, $matches) !== false) {
            $headers = array();

            $matches = $matches[1];

            foreach ($matches as $match) {
                $headers[] = $match;
            }

            $curl_opts[CURLOPT_HTTPHEADER] = $headers;

            if (strpos($command, '--data') !== false
                && preg_match('/(?:--data\s\")(.*?)(?=\")/s', $command, $matches) === 1) {
                    $curl_opts[CURLOPT_POST] = 1;
                    $curl_opts[CURLOPT_POSTFIELDS] = $matches[1];
            } else {
                $curl_opts[CURLOPT_CUSTOMREQUEST] = 'GET';
            }
        }
    }

    return $curl_opts;
}

/**
 * @param array $options
 * @return string
 */
function curl_request($options)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    foreach ($options as $key => $value) {
        curl_setopt($ch, $key, $value);
    }

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        $result = 'Error:' . curl_error($ch);
    }

    $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

    curl_close($ch);

    $charset = detect_charset($result, $content_type);

    if (!empty($charset) && strtoupper($charset) != "UTF-8") {
        $result = mb_convert_encoding($result, 'UTF-8', $charset);
    }

    return $result;
}

/**
 * @param string $result
 * @param string $content_type
 * @return string
 */
function detect_charset($result, $content_type)
{
    /* 1: HTTP Content-Type: header */
    if (preg_match('@([\w/+]+)(;\s*charset=(\S+))?@i', $content_type, $matches) === 1) {
        if (isset($matches[3])) {
            return $matches[3];
        }
    }

    /* 2: <meta> element in the page */
    if (preg_match('@<meta.+?http-equiv=\"Content-Type\".*?/?>@si', $result, $matches) === 1) {
        if (isset($matches[0])) {
            $meta = $matches[0];
        }

        if (isset($meta) && preg_match('@content="([\w/]+)(;\s*charset=([^\s"]+))?@si', $meta, $matches) === 1) {
            if (isset($matches[3])) {
                return $matches[3];
            }
        }
    }

    /* 3: <xml> element in the page */
    if (preg_match('@<\?xml.+encoding="([^\s"]+)@si', $result, $matches) === 1) {
        if (isset($matches[1])) {
            return $matches[1];
        }
    }

    /* 4: PHP's heuristic detection */
    if (!isset($charset)) {
        $encoding = mb_detect_encoding($result);
        if ($encoding) {
            return $encoding;
        }
    }

    /* 5: Default for HTML */
    if (!isset($charset)) {
        if (strstr($content_type, "text/html") === 0) {
            return "ISO 8859-1";
        }
    }

    return null;
}

/**
 * @param string $haystack
 * @param string $needle
 * @param bool $case
 * @return bool
 */
function start_with($haystack, $needle, $case = true)
{
    if ($case) {
        return (strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
    }

    return (strcasecmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
}


$result = '';
$response = '';

$requestStrings = explode('|', urldecode($_SERVER['QUERY_STRING']));

if (start_with($requestStrings[0], 'curl')) {
    $response = curl_request(parse_curl_command($requestStrings[0]));
} else {
    $response = curl_request(array(
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_URL => $requestStrings[0]
    ));
}

if (count($requestStrings) == 1) {
    $result = $response;
} else {
    if (strpos($requestStrings[1], '.*?') !== false) {
        if (preg_match_all("@$requestStrings[1](.*?)$requestStrings[2]@s", $response, $matches) !== false) {
            $result = $matches[1][0];
        }
    } else {
        if (empty($requestStrings[1]) && empty($requestStrings[2])) {
            $result = $response;
        } else {
            if (($start = strpos($response, $requestStrings[1])) !== false) {
                $start += strlen($requestStrings[1]);
                if (($end = strpos($response, $requestStrings[2], $start)) !== false) {
                    $result = substr($response, $start, $end - $start);
                }
            }
        }
    }
}

echo $result;

