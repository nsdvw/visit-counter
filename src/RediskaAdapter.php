<?php

namespace VisitCounter;

class RediskaAdapter extends RedisAdapter
{
    public function setnx($keyName, $expire = 0, $value = '')
    {
        $command = new Rediska_Command_Set(
            $this->client,
            'Set',
            array($keyName, $value, false)
        );
        $command();
        if ($expire) {
            $key = new \Rediska_Key($keyName);
            $key->expire($expire);
        }
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
}
