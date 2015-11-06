<?php

namespace VisitCounter;

class PredisAdapter extends RedisAdapter
{
    public function set($key, $value = '', $expire = 0)
    {
        $this->client->set($key, $value);
        if ($expire) $this->client->expire($key, $expire);
    }

    public function exists($key)
    {
        return $this->client->exists($key);
    }

    public function rpush($key, $value);
    {
        $this->client->rpush($key, $value);
    }

    public function llen($key)
    {
        return $this->client->llen($key);
    }

    public function lrange($key, $start = 0, $end = -1)
    {
        return $this->client->lrange($key, $start, $end);
    }

    public function ltrim($key, $start, $end = -1)
    {
        $this->client->ltrim($key, $start, $end);
    }
}
