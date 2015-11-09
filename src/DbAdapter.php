<?php

namespace VisitCounter;

abstract class DbAdapter
{
    protected $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    abstract public function save(array $data);
}
