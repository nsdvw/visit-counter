<?php

namespace VisitCounter\Db;

class PdoAdapter extends DbAdapter
{
    protected $pk = 'id';
    protected $tblName;
    protected $colName;

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

    public function setPk($pk = 'id')
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
}
