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

    if (strpos($command, ' -i') !== false) {
        $curl_opts[CURLOPT_HEADER] = true;
    }

    if (strpos($command, ' -L') !== false) {
        $curl_opts[CURLOPT_FOLLOWLOCATION] = true;
    }

    if (preg_match("/(?:\")(.*?)(?=\")/s", $command, $matches) === 1) {
        $curl_opts[CURLOPT_URL] = $matches[1];

        if (preg_match_all('/(?:-H\s\")(.*?)(?=\")/s', $command, $matches) !== false) {
            $headers = array();

            $matches = $matches[1];

            foreach ($matches as $header) {
                if(start_with(trim($header), 'accept-encoding', false)) {
                    $headerValue = preg_split('/(:|\s)/', $header);
                    $curl_opts[CURLOPT_ENCODING] = trim($headerValue[1]);
                } else {
                    $headers[] = $header;
                }
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

    curl_setopt_array($ch, $options);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        $result = 'Error:' . curl_error($ch);
        error_log('CURL '.$result);
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
$curlResponse = '';

$requestStrings = explode('|', urldecode($_SERVER['QUERY_STRING']));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = file_get_contents('php://input');
    $requestStrings = explode('|', urldecode(substr($postData, 2)));
}

if (!empty($requestStrings)) {
    if (strpos($requestStrings[0], 'curlorig') === 0) {
        $curlResponse = shell_exec('curl ' . substr($requestStrings[0], 9)) ?: '';
    } else if (start_with($requestStrings[0], 'curl')) {
        $curlResponse = curl_request(parse_curl_command($requestStrings[0]));
    } else {
        $curlResponse = curl_request(array(
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_URL => $requestStrings[0]
        ));
    }

    if (count($requestStrings) == 1) {
        $result = $curlResponse;
    } else {
        if (strpos($requestStrings[1], '.*?') === false) {
            if (empty($requestStrings[1]) && empty($requestStrings[2])) {
                $result = $curlResponse;
            } else {
                if (($start = strpos($curlResponse, $requestStrings[1])) !== false) {
                    $start += strlen($requestStrings[1]);
                    if (($end = strpos($curlResponse, $requestStrings[2], $start)) !== false) {
                        $result = substr($curlResponse, $start, $end - $start);
                    }
                }
            }
        } else {
            if (preg_match_all("@$requestStrings[1](.*?)$requestStrings[2]@s", $curlResponse, $matches) !== false) {
                $result = $matches[1][0];
            }
        }
    }
}

echo $result;

