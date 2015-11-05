<?php

namespace VisitCounter;

class DbAdapter
{
    public static function getConnection(array $options)
    {
        return new \PDO($options['dsn'], $options['user'], $options['password']);
    }
}
