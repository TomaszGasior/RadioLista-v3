<?php

namespace App\Doctrine\Middleware;

use Doctrine\DBAL\Connection\StaticServerVersionProvider;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;
use Doctrine\DBAL\Platforms\SqlitePlatform;

/**
 * The following middleware enables foreign keys when using SQLite database.
 * This makes SQLite behavior in testing and development environment consistent
 * with MySQL database used in production or containerized environment.
 *
 * This class has been copy-pasted from Doctrine DBAL.
 *
 * @see \Doctrine\DBAL\Driver\AbstractSQLiteDriver\Middleware\EnableForeignKeys
 */
class SqliteForeignKeyMiddleware implements Middleware
{
    public function wrap(Driver $driver): Driver
    {
        $platform = $driver->getDatabasePlatform(new StaticServerVersionProvider(''));
        if (!$platform instanceof SqlitePlatform) {
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
