<?php

namespace VisitCounter;

abstract class RedisAdapter
{
    protected $client;
    protected $keyPrefix;
    protected $keyExpire;

    public function __construct(
        $client,
        $config = array('keyPrefix'=>'VisitCounter', 'keyExpire'=>0))
    {
        $this->client = $client;
        $this->keyPrefix = $config['keyPrefix'];
        $this->keyExpire = $config['keyExpire'];
    }

    abstract public function set($keyName, $value, $expire = 0);
    abstract public function exists($keyName);
    abstract public function rpush($listName, $value);
    abstract public function llen($listName);
    abstract public function lrange($listName, $start = 0, $end = -1);
    abstract public function ltrim($listName, $start, $end = -1);
}
