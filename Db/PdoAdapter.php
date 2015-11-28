<?php

namespace VisitCounter\Db;

class PdoAdapter extends DbAdapter
{
    protected $pk = 'id';
    protected $tblName;
    protected $colName;

    public function save(array $visitsPages)
    {
        if (!$this->tblName or !$this->colName) {
            $message = "Properties tblName and colName are mandatory.";
            throw new \VisitCounter\Exception\Exception($message);
        }
        try {
            foreach ($visitsPages as $visitCount => $pages) {
                $pageList = implode(',', $pages);
                $sql = "UPDATE {$this->tblName}
                        SET {$this->colName} = {$this->colName} + $visitCount
                        WHERE {$this->pk} IN ({$pageList})";
                $this->connection->prepare($sql);
                $this->connection->execute();
            }
        } catch (\PDOException $e) {
            throw new \VisitCounter\Exception\Exception($e->getMessage(), 0, $e);
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
