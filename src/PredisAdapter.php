<?php

namespace VisitCounter;

class PredisAdapter extends RedisAdapter
{
    public function setnx($key, $expire = 0, $value = '')
    {
        $this->client->setnx($key, $value);
        if ($expire) $this->client->expire($key, $expire);
    }

    public function rpush($key, $value)
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
