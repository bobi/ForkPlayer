<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 28.01.2017
 * Time: 14:16
 */

namespace ForkPlayer\Playlist;


class PlaylistBuilder
{
    /**
     * @var Playlist
     */
    private $playlist;

    /**
     * PlaylistBuilder constructor.
     */
    function __construct()
    {
        $this->playlist = new Playlist();
    }

    /**
     * @param Item[] $items
     * @param string $nextPage
     * @return Playlist
     */
    public static function with($items, $nextPage)
    {
        $resp = new Playlist;

        $resp->setItems($items);
        $resp->setNextPage($nextPage);

        return $resp;
    }

    /**
     * @param Item[] $items
     * @return PlaylistBuilder
     */
    public function withItems($items)
    {
        $this->playlist->setItems($items);

        return $this;
    }

    /**
     * @param string $nextPage
     * @return PlaylistBuilder
     */
    public function withNextPage($nextPage)
    {
        $this->playlist->setNextPage($nextPage);

        return $this;
    }

    /**
     * @param string $name
     * @return PlaylistBuilder
     */
    public function withName($name)
    {
        $this->playlist->setName($name);

        return $this;
    }

    /**
     * @return PlaylistBuilder
     */
    public function withIpTv()
    {
        $this->playlist->setIsIpTv(true);

        return $this;
    }

    /**
     * @param string $info
     * @return PlaylistBuilder
     */
    public function withGetInfo($info)
    {
        $this->playlist->setGetInfo($info);

        return $this;
    }

    /**
     * @param string $timeout
     * @return PlaylistBuilder
     */
    public function withTimeout($timeout)
    {
        $this->playlist->setTimeout($timeout);

        return $this;
    }

    /**
     * @return Playlist
     */
    public function build() {
        return $this->playlist;
    }
}