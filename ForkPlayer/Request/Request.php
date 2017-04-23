<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 19.01.2017
 * Time: 23:54
 */
namespace ForkPlayer\Request;

if (!defined('HTTP_URL_REPLACE')) {
    define('HTTP_URL_REPLACE', 1);
}
if (!defined('HTTP_URL_JOIN_PATH')) {
    define('HTTP_URL_JOIN_PATH', 2);
}
if (!defined('HTTP_URL_JOIN_QUERY')) {
    define('HTTP_URL_JOIN_QUERY', 4);
}
if (!defined('HTTP_URL_STRIP_USER')) {
    define('HTTP_URL_STRIP_USER', 8);
}
if (!defined('HTTP_URL_STRIP_PASS')) {
    define('HTTP_URL_STRIP_PASS', 16);
}
if (!defined('HTTP_URL_STRIP_AUTH')) {
    define('HTTP_URL_STRIP_AUTH', 32);
}
if (!defined('HTTP_URL_STRIP_PORT')) {
    define('HTTP_URL_STRIP_PORT', 64);
}
if (!defined('HTTP_URL_STRIP_PATH')) {
    define('HTTP_URL_STRIP_PATH', 128);
}
if (!defined('HTTP_URL_STRIP_QUERY')) {
    define('HTTP_URL_STRIP_QUERY', 256);
}
if (!defined('HTTP_URL_STRIP_FRAGMENT')) {
    define('HTTP_URL_STRIP_FRAGMENT', 512);
}
if (!defined('HTTP_URL_STRIP_ALL')) {
    define('HTTP_URL_STRIP_ALL', 1024);
}
if (!defined('HTTP_URL_ENCODE_QUERY')) {
    define('HTTP_URL_ENCODE_QUERY', 2048);
}
if (!defined('HTTP_URL_NORMALIZE_PATH')) {
    define('HTTP_URL_NORMALIZE_PATH', 4096);
}
if (!defined('HTTP_URL_ENCODE_PATH')) {
    define('HTTP_URL_ENCODE_PATH', 8192);
}

class Request
{
    /**
     * @var string
     */
    private $host;
    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $scheme;

    /**
     * @return string
     */
    public function getHost()
    {
        if (!isset($this->host)) {
            $this->host = $this->_getHost();
        }

        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        if (!isset($this->port)) {
            $this->port = $this->_getPort();
        }

        return $this->port;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        if (!isset($this->scheme)) {
            $this->scheme = $this->_getScheme();
        }

        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getHttpHost()
    {
        $scheme = $this->getScheme();
        $port = $this->getPort();
        if (('http' == $scheme && $port == 80) || ('https' == $scheme && $port == 443)) {
            return $this->getHost();
        }

        return $this->getHost() . ':' . $port;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->getScheme() . '://' . $this->getHttpHost() . '/' . FP_CONTEXT_PATH . '/';
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasParameter($key)
    {
        return isset($_GET[$key]) || isset($_POST[$key]);
    }

    /**
     * @param string $key
     * @return string
     */
    public function getParameter($key)
    {
        return isset($_GET[$key]) ? urldecode($_GET[$key]) : (isset($_POST[$key]) ? urldecode($_POST[$key]) : null);
    }

    /**
     * @return string
     */
    private function _getScheme()
    {
        $https = null;

        if (isset($_SERVER['HTTPS'])) {
            $https = $_SERVER['HTTPS'];
        }

        $isSecure = !empty($https) && 'off' !== strtolower($https);

        return $isSecure ? 'https' : 'http';
    }

    /**
     * @return int
     */
    private function _getPort()
    {
        if ($host = $_SERVER['HTTP_HOST']) {
            if ($host[0] === '[') {
                $pos = strpos($host, ':', strrpos($host, ']'));
            } else {
                $pos = strrpos($host, ':');
            }
            if (false !== $pos) {
                return (int)substr($host, $pos + 1);
            }

            return 'https' === $this->getScheme() ? 443 : 80;
        }

        return $_SERVER['SERVER_PORT'];
    }

    /**
     * @return string
     */
    private function _getHost()
    {
        $possibleHostSources = array('HTTP_X_FORWARDED_HOST', 'HTTP_HOST', 'SERVER_NAME', 'SERVER_ADDR');
        $sourceTransformations = array(
            "HTTP_X_FORWARDED_HOST" => function ($value) {
                $elements = explode(',', $value);

                return trim(end($elements));
            }
        );
        $host = '';
        foreach ($possibleHostSources as $source) {
            if (!empty($host)) {
                break;
            }

            if (empty($_SERVER[$source])) {
                continue;
            }
            $host = $_SERVER[$source];
            if (array_key_exists($source, $sourceTransformations)) {
                $host = $sourceTransformations[$source]($host);
            }
        }

        // Remove port number from host
        $host = preg_replace('/:\d+$/', '', $host);

        return trim($host);
    }

    /**
     * @param string $path
     * @param array $params
     * @return string
     */
    public function absoluteUrl($path = null, $params = null)
    {
        return $this->http_build_url(
            $this->getBaseUrl(),
            [
                'path' => $path,
                'query' => empty($params) ? '' : http_build_query($params)
            ],
            HTTP_URL_STRIP_AUTH
            | HTTP_URL_JOIN_PATH
            | HTTP_URL_JOIN_QUERY
            | HTTP_URL_STRIP_FRAGMENT
            | HTTP_URL_NORMALIZE_PATH
            | HTTP_URL_ENCODE_PATH
            | HTTP_URL_ENCODE_QUERY
        );
    }

    /**
     * Build a URL.
     *
     * The parts of the second URL will be merged into the first according to
     * the flags argument.
     *
     * @param mixed $url (part(s) of) an URL in form of a string or
     *                       associative array like parse_url() returns
     * @param mixed $parts same as the first argument
     * @param int $flags a bitmask of binary or'ed HTTP_URL constants;
     *                       HTTP_URL_REPLACE is the default
     * @param array $new_url if set, it will be filled with the parts of the
     *                       composed url like parse_url() would return
     * @return string
     */
    function http_build_url($url, $parts = array(), $flags = HTTP_URL_REPLACE, &$new_url = array())
    {
        is_array($url) || $url = parse_url($url);
        is_array($parts) || $parts = parse_url($parts);

        isset($url['query']) && is_string($url['query']) || $url['query'] = null;
        isset($parts['query']) && is_string($parts['query']) || $parts['query'] = null;

        $keys = array('user', 'pass', 'port', 'path', 'query', 'fragment');

        // HTTP_URL_STRIP_ALL and HTTP_URL_STRIP_AUTH cover several other flags.
        if ($flags & HTTP_URL_STRIP_ALL) {
            $flags |= HTTP_URL_STRIP_USER | HTTP_URL_STRIP_PASS
                | HTTP_URL_STRIP_PORT | HTTP_URL_STRIP_PATH
                | HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT;
        } elseif ($flags & HTTP_URL_STRIP_AUTH) {
            $flags |= HTTP_URL_STRIP_USER | HTTP_URL_STRIP_PASS;
        }

        // Schema and host are alwasy replaced
        foreach (array('scheme', 'host') as $part) {
            if (isset($parts[$part])) {
                $url[$part] = $parts[$part];
            }
        }

        if ($flags & HTTP_URL_REPLACE) {
            foreach ($keys as $key) {
                if (isset($parts[$key])) {
                    $url[$key] = $parts[$key];
                }
            }
        } else {
            if (isset($parts['path']) && ($flags & HTTP_URL_JOIN_PATH)) {
                if (isset($url['path']) && substr($parts['path'], 0, 1) !== '/') {
                    // Workaround for trailing slashes
                    $url['path'] .= 'a';
                    $url['path'] = rtrim(
                            str_replace(basename($url['path']), '', $url['path']),
                            '/'
                        ) . '/' . ltrim($parts['path'], '/');
                } else {
                    $url['path'] = $parts['path'];
                }
            }

            if (isset($parts['query']) && ($flags & HTTP_URL_JOIN_QUERY)) {
                if (isset($url['query'])) {
                    parse_str($url['query'], $url_query);
                    parse_str($parts['query'], $parts_query);

                    $url['query'] = http_build_query(
                        array_replace_recursive(
                            $url_query,
                            $parts_query
                        )
                    );
                } else {
                    $url['query'] = $parts['query'];
                }
            }

            if (isset($url['query']) && ($flags & HTTP_URL_ENCODE_QUERY)) {
                parse_str($url['query'], $url_query);

                array_walk($url_query, function (&$value) {
                    $value = urldecode($value);
                });

                $url['query'] = http_build_query($url_query);
            }
        }

        if (isset($url['path']) && $url['path'] !== '' && substr($url['path'], 0, 1) !== '/') {
            $url['path'] = '/' . $url['path'];
        }

        foreach ($keys as $key) {
            $strip = 'HTTP_URL_STRIP_' . strtoupper($key);
            if ($flags & constant($strip)) {
                unset($url[$key]);
            }
        }

        if ($flags & HTTP_URL_NORMALIZE_PATH) {
            //replace backslash with slash
            $url['path'] = preg_replace('/\\\\+/si', '/', $url['path']);
            //replace double slash with single slash
            $url['path'] = preg_replace('/\/+/si', '/', $url['path']);
        }

        if ($flags & HTTP_URL_ENCODE_PATH) {
            //encode path
            $url['path'] = implode("/", array_map("rawurlencode", explode("/", $url['path'])));
        }

        $parsed_string = '';

        if (!empty($url['scheme'])) {
            $parsed_string .= $url['scheme'] . '://';
        }

        if (!empty($url['user'])) {
            $parsed_string .= $url['user'];

            if (isset($url['pass'])) {
                $parsed_string .= ':' . $url['pass'];
            }

            $parsed_string .= '@';
        }

        if (!empty($url['host'])) {
            $parsed_string .= $url['host'];
        }

        if (!empty($url['port'])) {
            $parsed_string .= ':' . $url['port'];
        }

        if (!empty($url['path'])) {
            $parsed_string .= $url['path'];
        }

        if (!empty($url['query'])) {
            $parsed_string .= '?' . $url['query'];
        }

        if (!empty($url['fragment'])) {
            $parsed_string .= '#' . $url['fragment'];
        }

        $new_url = $url;

        return $parsed_string;
    }
}