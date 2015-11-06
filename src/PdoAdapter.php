<?php

namespace VisitCounter;

class PdoAdapter extends DbAdapter
{
    public function save(array $data)
    {
        foreach ($data as $count => $pages) {
            $pageList = implode(',', $pages);
            $sql = "UPDATE {$this->tblName}
                    SET {$this->colName} = {$this->colName} + $count
                    WHERE {$this->pk} IN ({$pageList})";
            $this->connection->prepare($sql);
            $this->connection->execute();
        }
    }    
}
