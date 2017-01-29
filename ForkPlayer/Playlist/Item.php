<?php

namespace ForkPlayer\Playlist;

/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 19.01.2017
 * Time: 0:38
 */
class Item
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $description;
    /**
     * @var string
     */
    private $link;
    /**
     * @var string
     */
    private $imageLink;

    /**
     * @var ItemType;
     */
    private $type;
    /**
     * Channel constructor.
     * @param string $name
     * @param string $description
     * @param string $link
     * @param string $imageLink
     * @param ItemType $type
     */
    public function __construct($name, $description, $link, $imageLink, $type)
    {
        $this->name = $name;
        $this->description = $description;
        $this->link = $link;
        $this->imageLink = $imageLink;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return string
     */
    public function getImageLink()
    {
        return $this->imageLink;
    }

    /**
     * @return ItemType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @param string $imageLink
     */
    public function setImageLink($imageLink)
    {
        $this->imageLink = $imageLink;
    }

    /**
     * @param ItemType $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}