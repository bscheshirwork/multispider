<?php

/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 18.10.16
 * Time: 4:45
 */
namespace Multispider;

class TaskData
{
    private $mask;

    private $path;

    /**
     * TaskData constructor.
     * @param $path
     * @param $mask
     */
    public function __construct($path = null, $mask = null)
    {
        $this->path = $path ?? '~/.';
        $this->mask = $mask;
    }


    /**
     * @param mixed $mask
     * @return TaskData
     */
    public function setMask($mask)
    {
        $this->mask = $mask;
        return $this;
    }

    /**
     * @param mixed $path
     * @return TaskData
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMask()
    {
        return $this->mask;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

}