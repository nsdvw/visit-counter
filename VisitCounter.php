<?php

namespace VisitCounter;

class VisitCounter
{
    protected $client;
    protected $db;

    protected $counterName = 'VisitCounter';
    protected $keyPrefix;
    protected $keyExpiration = 2592000;

    protected $perTransaction = 1000;

    public function __construct(Redis\RedisAdapterInterface $client)
    {
        $this->client = $client;
    }

    public function countVisit($pageID, $userIP)
    {
        if ($this->keyPrefix) {
            $keyName = "{$this->keyPrefix}:{$pageID}:{$userIP}";
        } else {
            $keyName = "{$pageID}:{$userIP}";
        }
        if ( !$this->client->setnx($keyName, $this->keyExpiration) ) return;
        $this->client->rpush($this->getQueueName(), $pageID);
        $this->client->hincrby($this->counterName, $pageID);
    }

    public function moveToDB(Db\DbAdapterInterface $db)
    {
        $this->db = $db;
        $queueLen = $this->client->llen($this->getQueueName());
        $batchCount = intval(floor($queueLen/$this->perTransaction));
        for ($i=0; $i < $batchCount; $i++) {
            $this->saveAndFlushCounter($this->perTransaction);
        }
        $remainder = $queueLen % $this->perTransaction;
        if ($remainder) {
            $this->saveAndFlushCounter($remainder);
        }
    }

    public function getDeltaVisits($pageID)
    {
        return $this->client->hmget($this->counterName, $pageID);
    }

    protected function saveAndFlushCounter($count)
    {
        $pages = $this->client->lrange($this->getQueueName(), 0, $count - 1);
        $visitsPages = $this->sortData($pages);
        $this->db->save($visitsPages);
        $this->client->ltrim($this->getQueueName(), $count);
        foreach ($visitsPages as $visitCount => $pages) {
            foreach ($pages as $pageID) {
                $this->client->hincrby($this->counterName, $pageID, -$visitCount);
            }
        }
    }

    protected function sortData(array $pages)
    {
        foreach (array_count_values($pages) as $pageID => $visited) {
            $visitsPages[$visited][] = $pageID;
        }
        return $visitsPages;
    }

    public function setPerTransaction($perTransaction)
    {
        $this->perTransaction = $perTransaction;
    }

    public function setKeyExpiration($keyExpiration)
    {
        $this->keyExpiration = $keyExpiration;
    }

    public function setKeyPrefix($keyPrefix)
    {
        $this->keyPrefix = $keyPrefix;
    }

    protected function getQueueName()
    {
        return $this->keyPrefix . 'Queue';
    }
}
