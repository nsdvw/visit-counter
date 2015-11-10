<?php

namespace VisitCounter;

abstract class RedisAdapter
{
    protected $client;

    protected $keyPrefix;
    protected $keyExpiration = 0;

    public function __construct($client)
    {
        $this->client = $client;
    }

    abstract public function addUniqueVisit($pageID, $userIP);
    abstract public function appendToQueue($pageID);
    abstract public function getQueueLen();
    abstract public function getFromQueue($count);
    abstract public function deleteFromQueue($count);

    public function setKeyExpiration($keyExpiration)
    {
        $this->keyExpiration = $keyExpiration;
    }

    public function setKeyPrefix($keyPrefix)
    {
        $this->keyPrefix = $keyPrefix;
    }

    public function getQueueName()
    {
        return $this->keyPrefix . 'Queue';
    }
}
