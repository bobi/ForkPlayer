<?php

/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 20.01.2017
 * Time: 13:51
 */
namespace ForkPlayer;

use ForkPlayer\Api\PluginContext;
use ForkPlayer\Api\SiteContext;
use ForkPlayer\Playlist\Item;
use ForkPlayer\Playlist\ItemType;
use ForkPlayer\Playlist\Playlist;
use ForkPlayer\Plugins\Plugins;
use ForkPlayer\Request\Request;
use ForkPlayer\Utils\OutputFormat;
use ForkPlayer\Utils\Serializer;
use ForkPlayer\Utils\StringUtils;

class ForkPlayer
{
    /**
     * @var Plugins
     */
    private $plugins;

    /**
     * ForkPlayer constructor.
     */
    public function __construct()
    {
        $this->plugins = new Plugins();
    }

    /**
     * @return string
     */
    public function dispatch()
    {
        $req = new Request();

        $pluginId = $req->getParameter('plugin');

        if (empty($pluginId)) {
            return self::serializePlaylist($this->pluginsPlaylist($req));
        } else {
            $plugin = $this->plugins->get($pluginId);

            if (isset($plugin)) {
                return self::serializePlaylist($plugin->dispatch(new PluginContext($req, $pluginId)));
            } else {
                return self::serializePlaylist(Playlist::emptyResponse());
            }
        }
    }

    /**
     * @param Request $request
     * @return Playlist
     */
    public function pluginsPlaylist($request)
    {
        $items = array();

        foreach ($this->plugins->getPlugins() as $plugin) {
            $logo = $plugin->getLogo();

            if (!StringUtils::startsWith($logo, 'http')) {
                $logo = $request->absoluteUrl($plugin->getLogo());
            }

            array_push(
                $items,
                new Item(
                    $plugin->getName(),
                    $plugin->getDescription(),
                    PluginContext::createPluginUrl($request, $plugin->getId()),
                    $logo,
                    ItemType::from(ItemType::DIRECTORY)
                )
            );
        }

        return Playlist::builder()->withName("All Plugins")->withItems($items)->build();
    }

    /**
     * @param Playlist $playlist
     * @return string
     */
    private static function serializePlaylist($playlist)
    {
        $outputFormat = new OutputFormat();

        header($outputFormat->getHeader(), true);

        if ($outputFormat::JSON == $outputFormat->getFormat()) {
            return Serializer::toJson($playlist);
        } else {
            return Serializer::toXml($playlist);
        }
    }
}