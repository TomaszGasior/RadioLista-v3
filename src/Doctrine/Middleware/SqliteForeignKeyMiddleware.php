<?php

namespace App\Doctrine\Middleware;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;
use Doctrine\DBAL\Platforms\SqlitePlatform;

class SqliteForeignKeyMiddleware implements Middleware
{
    public function wrap(Driver $driver): Driver
    {
        if (false === $driver->getDatabasePlatform() instanceof SqlitePlatform) {
            return $driver;
        }

        return new class ($driver) extends AbstractDriverMiddleware {
            public function connect(array $params): Connection
            {
                $connection = parent::connect($params);
                $connection->exec('PRAGMA foreign_keys=ON');

                return $connection;
            }
        };
    }
}
