<?php
namespace ForkPlayer\Playlist;

/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 19.01.2017
 * Time: 13:07
 */
class Playlist
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Item[]
     */
    private $items;

    /**
     * @var string
     */
    private $nextPage;

    /**
     * @return PlaylistBuilder
     */
    public static function builder()
    {
        $builder = new PlaylistBuilder();

        return $builder;
    }

    /**
     * @param Item[] $items
     * @return Playlist
     */
    public static function withItems($items)
    {
        $resp = new Playlist;

        $resp->setItems($items);

        return $resp;
    }

    /**
     * @param string $name
     * @param Item[] $items
     * @return Playlist
     */
    public static function withName($name, $items)
    {
        $resp = new Playlist;

        $resp->setName($name);
        $resp->setItems($items);

        return $resp;
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
     * @return Playlist
     */
    public static function emptyResponse()
    {
        return new Playlist();
    }

    /**
     * @return Playlist
     */
    public static function notFoundResponse()
    {
        return self::withItems(
            array(
                new Item(
                    "Контент не найден или неопубликован",
                    "Контент не найден или неопубликован",
                    "",
                    "",
                    ItemType::from(ItemType::FILE)
                )
            )
        );
    }

    /**
     * @return Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param Item[] $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function getNextPage()
    {
        return $this->nextPage;
    }

    /**
     * @param string $nextPage
     */
    public function setNextPage($nextPage)
    {
        $this->nextPage = $nextPage;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}