<?php

namespace VisitCounter\Redis;

class RediskaAdapter extends RedisAdapter
{
    public function __construct(\Rediska $client)
    {
        $this->client = $client;
    }

    public function setnx($keyName, $expire, $value = '')
    {
        $command = new \Rediska_Command_Set(
            $this->client,
            'Set',
            array($keyName, $value, false)
        );
        if ( !$command->execute() ) return false; 
        if ($expire) {
            $key = new \Rediska_Key($keyName);
            $key->expire($expire);
        }
        return true;
    }

    public function rpush($listName, $value)
    {
        $key = new \Rediska_Key_List($listName);
        return $key->append($value);
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

    public function ltrim($listName, $start = 0, $end = -1)
    {
        $key = new \Rediska_Key_List($listName);
        return $key->truncate($start, $end);
    }

    public function hincrby($hashName, $field, $count = 1)
    {
        $key = new \Rediska_Key_Hash($hashName);
        if ($key->increment($field, $count)) return true;
        return false;
    }

    public function hget($hashName, $field)
    {
        $key = new \Rediska_Key_Hash($hashName);
        return $key->get($field);
    }
}
