<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 28.01.2017
 * Time: 15:28
 */

namespace ForkPlayer\Api;


use ForkPlayer\Playlist\Playlist;

interface Plugin
{
    /**
     * @return int
     */
    function getOrder();

    /**
     * @return string
     */
    function getId();
    /**
     * @return string
     */
    function getName();
    /**
     * @return string
     */
    function getDescription();
    /**
     * @return string
     */
    function getLogo();

    /**
     * @param PluginContext $context
     * @return Playlist
     */
    function dispatch($context);
}