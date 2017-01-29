<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 20.01.2017
 * Time: 20:52
 */

namespace ForkPlayer\Playlist;

class ItemType
{
    const DIRECTORY = 0;
    const FILE = 1;
    const SEARCH = 2;

    /**
     * @var int
     */
    private $type;

    /**
     * ChannelType constructor.
     * @param int $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }


    public static function from($type)
    {
        return new ItemType($type);
    }

    /**
     * @return int
     */
    public function get()
    {
        return $this->type;
    }
}