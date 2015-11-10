<?php

namespace VisitCounter;

class RediskaAdapter extends RedisAdapter
{
    public function addUniqueVisit($pageID, $userIP)
    {
        if ($this->keyPrefix) {
            $keyName = "{$this->keyPrefix}:{$pageID}:{$userIP}";
        } else {
            $keyName = "{$pageID}:{$userIP}";
        }
        $command = new \Rediska_Command_Set(
            $this->client,
            'Set',
            array($keyName, '', false)
        );
        if (!$command->execute()) return false;
        if ($this->keyExpiration) {
            $key = new \Rediska_Key($keyName);
            $key->expire($this->keyExpiration);
        }
        return true;
    }

    public function appendToQueue($pageID)
    {
        $key = new \Rediska_Key_List($this->getQueueName());
        if ($key->append($pageID)) return true;
        return false;
    }

    public function getQueueLen()
    {
        $key = new \Rediska_Key_List($this->getQueueName());
        return $key->getLength();
    }

    public function getFromQueue($count)
    {
        $key = new \Rediska_Key_List($this->getQueueName());
        return $key->getValues(0, $count - 1);
    }

    public function deleteFromQueue($count)
    {
        $key = new \Rediska_Key_List($this->getQueueName());
        $key->truncate(0, $count - 1);
    }
}
