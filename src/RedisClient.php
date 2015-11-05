<?php

namespace VisitCounter;

abstract class RedisClient
{
    protected $instance;

    public function __construct($options)
    {
        $this->setInstance($options);
    }

    abstract public function set($keyName, $value, $expire = 0);
    abstract public function exists($keyName);
    abstract public function rpush($listName, $value);
    abstract public function llen($listName);
    abstract public function lrange($listName, $start = 0, $end = -1);
    abstract public function ltrim($listName, $start, $end = -1);

    abstract protected function setInstance($options);
}
