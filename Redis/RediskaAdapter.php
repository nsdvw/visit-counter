<?php

namespace VisitCounter\Redis;

class RediskaAdapter extends RedisAdapter
{
    protected $errorMessage = "Redis error";

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
        if ( !$command->execute() ) throw new \Exception($this->errorMessage);
        if ($expire) {
            $key = new \Rediska_Key($keyName);
            $key->expire($expire);
        }
        return true;
    }

    public function rpush($listName, $value)
    {
        $key = new \Rediska_Key_List($listName);
        if( !$key->append($value) ) throw new \Exception($this->errorMessage);
        return true;
    }

    public function llen($listName)
    {
        $key = new \Rediska_Key_List($listName);
        $length = $key->getLength();
        if(!$length) throw new \Exception($this->errorMessage);
        return $length;
    }

    public function lrange($listName, $start = 0, $end = -1)
    {
        $key = new \Rediska_Key_List($listName);
        $result = $key->getValues($start, $end)
        if(!$result) throw new \Exception($this->errorMessage);
        return $result;
    }

    public function ltrim($listName, $start = 0, $end = -1)
    {
        $key = new \Rediska_Key_List($listName);
        if( !$key->truncate($start, $end) ) {
            throw new \Exception($this->errorMessage);
        }
        return true;
    }

    public function hincrby($hashName, $field, $count = 1)
    {
        $key = new \Rediska_Key_Hash($hashName);
        if (!$key->increment($field, $count)) {
            throw new \Exception($this->errorMessage);
        }
        return true;
    }

    public function hget($hashName, $field)
    {
        $key = new \Rediska_Key_Hash($hashName);
        $result = $key->get($field);
        if (!$result) throw new \Exception($this->errorMessage);
        return true;
    }
}
