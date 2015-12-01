<?php

namespace VisitCounter\Db;

class PdoAdapter implements DbAdapterInterface
{
    private $connection;

    protected $pk;
    protected $tblName;
    protected $colName;

    public function __construct($connection, $tblName, $colName, $pk = 'id')
    {
        $this->connection = $connection;
        $this->tblName = $tblName;
        $this->colName = $colName;
        $this->pk = $pk;
    }

    public function save(array $visitsPages)
    {
        if (!$this->tblName or !$this->colName) {
            $message = "Properties tblName and colName are mandatory.";
            throw new \VisitCounter\Exception\RedisException($message);
        }
        try {
            foreach ($visitsPages as $visitCount => $pages) {
                $pageList = implode(',', $pages);
                $sql = "UPDATE {$this->tblName}
                        SET {$this->colName} = {$this->colName} + $visitCount
                        WHERE {$this->pk} IN ({$pageList})";
                $sth = $this->connection->prepare($sql);
                $sth->execute();
            }
        } catch (\PDOException $e) {
            throw new \VisitCounter\Exception\DbException($e->getMessage(), 0, $e);
        }
    }
}
