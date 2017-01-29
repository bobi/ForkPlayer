<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 28.01.2017
 * Time: 16:59
 */

namespace ForkPlayer\Api;


use ForkPlayer\Request\Request;

class PluginContext
{
    /**
     * @var Request
     */
    private $request;

    private $pluginId;

    /**
     * PluginContext constructor.
     * @param Request $request
     * @param $pluginId
     */
    public function __construct($request, $pluginId)
    {
        $this->request = $request;
        $this->pluginId = $pluginId;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param array $params
     * @return string
     */
    public function pluginUrl($params = null) {
        return self::createPluginUrl($this->request, $this->pluginId, $params);
    }

    /**
     * @param Request $request
     * @param string $pluginId
     * @param array $params
     * @return mixed
     */
    public static function createPluginUrl($request, $pluginId, $params = null) {
        return $request->absoluteUrl('plugin/' . $pluginId, $params);
    }
}