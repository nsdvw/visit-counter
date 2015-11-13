<?php

namespace VisitCounter\Redis;

abstract class RedisAdapter
{
    protected $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    abstract public function setnx($keyName, $expire, $value = '');
    abstract public function rpush($listName, $value);
    abstract public function llen($listName);
    abstract public function lrange($listName, $start = 0, $end = -1);
    abstract public function ltrim($listName, $start, $end = -1);
}
