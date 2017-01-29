<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 28.01.2017
 * Time: 15:01
 */

namespace ForkPlayer\Plugins;


use ForkPlayer\Api\Plugin;
use ReflectionClass;

class Plugins
{
    /**
     * @var Plugin[]
     */
    private $plugins;

    /**
     * Plugins constructor.
     */
    public function __construct()
    {
        $this->loadPlugins();
    }

    /**
     * @param string $pluginId
     * @return Plugin
     */
    public function get($pluginId)
    {
        return isset($this->plugins[$pluginId]) ? $this->plugins[$pluginId] : null;
    }

    /**
     * @return Plugin[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @return void
     */
    private function loadPlugins()
    {
        $this->loadPluginScripts();

        $plugins = array();

        $classes = get_declared_classes();

        foreach ($classes as $class) {
            $reflectClass = new ReflectionClass($class);

            if ($reflectClass->implementsInterface('\ForkPlayer\Api\Plugin') && !$reflectClass->isAbstract()) {
                /**@var Plugin $plugin */
                $plugin = $reflectClass->newInstance();

                $plugins[$plugin->getId()] = $plugin;
            }
        }

        uasort($plugins, function ($a, $b) {
            /** @var $a Plugin */
            /** @var $b Plugin */

            return $a->getOrder() - $b->getOrder();
        });

        $this->plugins = $plugins;
    }

    /**
     * @return void
     */
    private function loadPluginScripts()
    {
        $dir = dir(realpath(__DIR__));

        while (false !== ($filename = $dir->read())) {
            $pluginDir = realpath($dir->path . '/' . $filename);
            if (is_dir($pluginDir)) {
                $script = realpath($pluginDir . '/plugin.php');
                if ($script !== false) {
                    require_once $script;
                }
            }
        }

        $dir->close();
    }
}