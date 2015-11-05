<?php

namespace VisitCounter;

class Rediska extends RedisClient
{
    public function set($keyName, $value = '', $expire = 0)
    {
        $key = new \Rediska_Key($keyName);
        $key->setValue($value);
        if ($expire) $key->expire($expire);
    }

    public function exists($keyName)
    {
        return $this->instance->exists($keyName);
    }

    public function rpush($listName, $value)
    {
        $key = new \Rediska_Key_List($listName);
        $key->append($value);
    }

    public function llen($listName)
    {
        $key = new \Rediska_Key_List($listName);
        return $key->getLength();
    }

    public function lrange($listName, $start = 0, $end = -1)
    {
        $key = new \Rediska_Key_List($listName);
        return $key->getValues($start, $end);
    }

    public function ltrim($listName, $start, $end = -1)
    {
        $key = new \Rediska_Key_List($listName);
        $key->truncate($start, $end);
    }

    protected function setInstance($options)
    {
        $this->instance = new \Rediska($options);
    }
}
