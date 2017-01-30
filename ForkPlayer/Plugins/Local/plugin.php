<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 28.01.2017
 * Time: 15:12
 */

namespace ForkPlayer\Plugins\Local;

use ForkPlayer\Api\BasePlugin;
use ForkPlayer\Api\PluginContext;
use ForkPlayer\Playlist\Item;
use ForkPlayer\Playlist\ItemType;
use ForkPlayer\Playlist\Playlist;
use ForkPlayer\Utils\StringUtils;

class LocalPlugin extends BasePlugin
{
    const FP_LOCAL_VIDEO_PATH = 'FP_LOCAL_VIDEO_PATH';
    /**
     * @var string
     */
    private $root;
    /**
     * @var string
     */
    private $urlRoot = 'localvideo';

    /**
     * SiteInfo constructor.
     */
    public function __construct()
    {
        parent::__construct('NetworkResources', 'Network Resources', 'Network Resources', 'img/local-network.png');
        $this->root = realpath($_SERVER[self::FP_LOCAL_VIDEO_PATH]);
    }

    /**
     * @param PluginContext $context
     * @return Playlist
     */
    public function dispatch($context)
    {
        $path = $context->getRequest()->getParameter('path');

        if (empty($path)) {
            $path = $this->root;
        } else {
            $path = realpath($this->root . DIRECTORY_SEPARATOR . $path);
        }

        return $this->readDir($context, $path);
    }

    /**
     * @param PluginContext $context
     * @param string $basePath
     * @return Playlist
     */
    private function readDir($context, $basePath) {
        $dir = dir(realpath($basePath));

        $items = array();

        while (false !== ($filename = $dir->read())) {
            if (!StringUtils::startsWith($filename, '.')) {
                $path = realpath($dir->path . '/' . $filename);
                if (is_dir($path)) {
                    array_push(
                        $items,
                        new Item(
                            $filename,
                            '',
                            $context->pluginUrl(array('path' => $this->relativePath($path))),
                            '',
                            ItemType::from(ItemType::DIRECTORY)
                        )
                    );
                } else {
                    array_push(
                        $items,
                        new Item(
                            $filename,
                            '',
                            $context->getRequest()->absoluteUrl($this->urlRoot . '/' . ($this->relativePath($path))),
                            '',
                            ItemType::from(ItemType::FILE)
                        )
                    );
                }
            }
        }

        $dir->close();

        uasort($items, function ($a, $b) {
            /** @var $a Item */
            /** @var $b Item */

            if ($a->getType()->get() != $b->getType()->get()) {
                if ($a->getType()->get() == ItemType::DIRECTORY) {
                    return -1;
                } elseif ($b->getType()->get() == ItemType::DIRECTORY) {
                    return 1;
                }
            }

            return strcasecmp($a->getName(), $b->getName());
        });

        return Playlist::builder()->withItems($items)->build();
    }

    private function relativePath($path) {
        $fullPath = str_replace('\\', "/", realpath($path));
        $root = str_replace('\\', "/", realpath($this->root));

        return str_replace($root, '', $fullPath);
    }
}