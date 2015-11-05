<?php

namespace VisitCounter;

class VisitCounter
{
    const REDISKA = 'rediska';
    const PREDIS = 'predis';
    const PER_TRANSACTION = 1000;
    protected $clientInstance;
    protected $dbConnection;
    protected $keyPrefix;

    public function __construct(
        $clientName,
        $redisOptions,
        $keyPrefix = 'visitCounter')
    {
        $this->keyPrefix = $keyPrefix;
        switch ($clientName) {
            case self::REDISKA :
                $this->clientInstance = new Rediska($redisOptions);
                break;
            case self::PREDIS :
                $this->clientInstance = new Predis($redisOptions);
                break;
            default : throw new Exception('Client is not supported.');
        }
    }

    public function appendToQueue($pageID, $userIP, $expire = 0, $keyValue = '')
    {
        $uniqueVisit = "{$this->keyPrefix}:{$pageID}:{$userIP}";
        if ($this->clientInstance->exists($uniqueVisit)) return;
        $this->clientInstance->set($uniqueVisit, $keyValue, $expire);
        $this->clientInstance->rpush($this->getQueueName(), $pageID);
    }

    public function transferToDB()
    {
        $queueLen = $this->instance->llen($this->getQueueName());
        $bunchCount = intval(floor($queueLen/self::PER_TRANSACTION));
        for ($i=0; $i < $bunchCount; $i++) {
            $bunchOfPages = $this->instance->lrange($this->getQueueName);
            $sortedData = $this->prepareData($bunchOfPages);
            $this->saveToDB($sortedData);
            $this->deleteFromQueue($this->getQueueName(), self::PER_TRANSACTION); // +++
        }
    }

    protected function deleteFromQueue($queueName, $count)
    {
        $this->clientInstance->ltrim($queueName, $count);
    }

    protected function prepareData(array $data)
    {
        $firstStep = array_count_values($data);
        foreach ($firstStep as $key => $value) {
            $sortedData[$value][] = $key;
        }
        return $sortedData;
    }

    protected function saveToDB(array $data, $tblName, $columnName, $pk = 'id')
    {
        foreach ($data as $count => $pages) {
            $pageList = implode(',', $pages);
            $sql = "UPDATE $tblName SET $columnName = $columnName + $count
                    WHERE $pk IN ({$pageList})";
            $this->dbConnection->prepare($sql);
            $this->dbConnection->execute();
        }
    }

    protected function connectToDb($dbOptions)
    {
        $this->dbConnection = DbAdapter::getConnection($dbOptions);
    }

    protected function getQueueName()
    {
        return "{$this->keyPrefix}Queue";
    }
}
