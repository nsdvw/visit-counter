<?php

namespace VisitCounter\Db;

interface DbAdapterInterface
{
    public function save(array $data);
}
