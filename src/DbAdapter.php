<?php

namespace VisitCounter;

abstact class DbAdapter
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
}
