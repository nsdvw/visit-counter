<?php

namespace VisitCounter;

abstract class DbAdapter
{
    protected $connection;
    protected $pk;
    protected $tblName;
    protected $colName;

    public function __construct($connection, $adapterConfig = array())
    {
        $this->connection = $connection;
        if (isset($adapterConfig['pk'])) {
            $this->pk = $adapterConfig['pk'];
        }
        if (isset($adapterConfig['tblName'])) {
            $this->tblName = $adapterConfig['tblName'];
        }
        if (isset($adapterConfig['colName'])) {
            $this->tblName = $adapterConfig['colName'];
        }
    }

    abstract public function save(array $data);

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
}
