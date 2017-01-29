<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 28.01.2017
 * Time: 15:38
 */

namespace ForkPlayer\Api;


abstract class BasePlugin implements Plugin
{
    /**
     * @var int
     */
    private $order;

    /**
     * @var string
     */
    private $id;

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
    private $logo;

    /**
     * BasePlugin constructor.
     * @param string $id
     * @param string $name
     * @param string $description
     * @param string $logo
     * @param int $order
     */
    protected function __construct($id, $name, $description, $logo, $order = PHP_INT_MAX)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->logo = $logo;
        $this->order = $order;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
    public function getLogo()
    {
        return $this->logo;
    }
}