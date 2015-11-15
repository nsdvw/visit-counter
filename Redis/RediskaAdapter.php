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
}
