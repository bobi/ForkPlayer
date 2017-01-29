<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 19.01.2017
 * Time: 23:54
 */
namespace ForkPlayer\Request;

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
    public function getPath() {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->getScheme() . '://' . $this->getHttpHost() . '/' . FP_CONTEXT_PATH;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasParameter($key)
    {
        $parameter = $this->getParameter($key);

        return !empty($parameter);
    }

    /**
     * @param string $key
     * @return string
     */
    public function getParameter($key)
    {
        return isset($_GET[$key]) ? $_GET[$key] : (isset($_POST[$key]) ? $_POST[$key] : null);
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
    public function absoluteUrl($path = null, $params = null) {
        $url = $this->getBaseUrl();

        if (!empty($path)) {
            $url .= $path;
        }

        if (!empty($params)) {
            $url .= '?' .http_build_query($params);
        }

        return $url;
    }
}