<?php

namespace VisitCounter;

class VisitCounter
{
    protected $client;
    protected $db;

    protected $keyPrefix;
    protected $keyExpiration = 0;

    protected $perTransaction = 1000;

    public function __construct(Redis\RedisAdapter $client)
    {
        $this->client = $client;
    }

    public function setDb(Db\DbAdapter $dbAdapter)
    {
        $this->db = $dbAdapter;
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
    }

    public function moveToDB()
    {
        $queueLen = $this->client->llen($this->getQueueName());
        $batchCount = intval(floor($queueLen/$this->perTransaction));
        for ($i=0; $i < $batchCount; $i++) {
            $this->moveBatch($this->perTransaction);
        }
        $remainder = $queueLen % $this->perTransaction;
        if ($remainder) {
            $this->moveBatch($remainder);
        }
    }

    protected function moveBatch($count)
    {
        $pages = $this->client->lrange(0, $count - 1);
        $visitsPages = $this->sortData($pages);
        $this->db->save($visitsPages);
        $this->client->ltrim($this->getQueueName(), $count);
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
