<?php

namespace VisitCounter;

class VisitCounter
{
    protected $perTransaction;
    protected $client;
    protected $db;

    public function __construct(RedisAdapter $redisAdapter, $perTransaction = 1000)
    {
        $this->client = $redisAdapter;
    }

    public function setDb(DbAdapter $dbAdapter)
    {
        $this->db = $dbAdapter;
    }

    public function considerVisit($pageID, $userIP)
    {
        $uniqueVisit = "{$this->client->getKeyPrefix()}:{$pageID}:{$userIP}";
        if ($this->client->exists($uniqueVisit)) return;
        $this->client->set($uniqueVisit, $this->client->getKeyExpiration());
        $this->client->rpush($this->getQueueName(), $pageID);
    }

    public function transferToDB()
    {
        $queueLen = $this->client->llen($this->getQueueName());
        $bunchCount = intval(floor($queueLen/$this->perTransaction));
        for ($i=0; $i < $bunchCount; $i++) {
            $bunchOfPages = $this->client->lrange($this->getQueueName);
            $sortedData = $this->prepareData($bunchOfPages);
            $this->db->save($sortedData);
            $this->deleteFromQueue($this->getQueueName(), $this->perTransaction);
        }
    }

    protected function deleteFromQueue($queueName, $count)
    {
        $this->client->ltrim($queueName, $count);
    }

    protected function prepareData(array $data)
    {
        foreach (array_count_values($data) as $key => $value) {
            $sortedData[$value][] = $key;
        }
        return $sortedData;
    }

    protected function getQueueName()
    {
        return "{$this->client->getKeyPrefix()}Queue";
    }
}
