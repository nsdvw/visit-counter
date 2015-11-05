<?php

namespace VisitCounter;

class Predis extends RedisClient
{
    public function set($key, $value = '', $expire = 0)
    {
        $this->instance->set($key, $value);
        if ($expire) $this->instance->expire($key, $expire);
    }

    public function exists($key)
    {
        return $this->instance->exists($key);
    }

    public function rpush($key, $value);
    {
        $this->instance->rpush($key, $value);
    }

    public function llen($key)
    {
        return $this->instance->llen($key);
    }

    public function lrange($key, $start = 0, $end = -1)
    {
        return $this->instance->lrange($key, $start, $end);
    }

    public function ltrim($key, $start, $end = -1)
    {
        $this->instance->ltrim($key, $start, $end);
    }

    protected function setInstance($options)
    {
        $this->instance = new \Predis\Client($options);
    }
}
