<?php

namespace VisitCounter;

class VisitCounter
{
    protected $client;
    protected $db;

    private $perTransaction = 1000;
    private $keyPrefix = '';
    private $keyExpiration = 0;
    private $pk;
    private $tblName;
    private $colName;

    public function __construct(RedisAdapter $redisAdapter)
    {
        $this->client = $redisAdapter;
    }

    public function setDb(DbAdapter $dbAdapter)
    {
        $this->db = $dbAdapter;
    }

    public function considerVisit($pageID, $userIP)
    {
        $uniqueVisit = "{$this->keyPrefix}:{$pageID}:{$userIP}";
        $setnx = $this->client->setnx($uniqueVisit, $this->keyExpiration);
        if (!$setnx) return;
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

    protected function deleteFromQueue($count)
    {
        $this->client->ltrim($this->getQueueName(), $count);
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
        return "{$this->keyPrefix}Queue";
    }

    public function setPerTransaction($perTransaction)
    {
        $this->perTransaction = $perTransaction;
    }

    public function setPk($pk)
    {
        $this->pk = $pk;
    }

    public function setTblName($tblName)
    {
        $this->tblName = $tblName;
    }

    public function setColName($colName)
    {
        $this->colName = $colName;
    }

    public function setKeyPrefix($keyPrefix)
    {
        $this->keyPrefix = $keyPrefix;
    }

    public function setKeyExpiration($keyExpiration)
    {
        $this->keyExpiration = $keyExpiration;
    }
}
