<?php

namespace VisitCounter;

class VisitCounter
{
    protected $client;
    protected $db;

    protected $perTransaction = 1000;

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
        if (!$this->client->addUniqueVisit()) return;
        $this->client->appendToQueue($pageID);
    }

    public function moveToDB()
    {
        $queueLen = $this->client->getQueueLen();
        $batchCount = intval(floor($queueLen/$this->perTransaction));
        for ($i=0; $i < $batchCount; $i++) {
            $this->moveBatch($this->perTransaction);
        }
        $remainder = $queueLen % $this->perTransaction;
        if ($remainder) {
            $this->moveBatch($remainder);
        }
    }

    public function moveBatch($count)
    {
        $pages = $this->client->getFromQueue($count);
        $visitsPages = $this->sortData($pages);
        $this->db->save($visitsPages);
        $this->client->deleteFromQueue($count);
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
}
