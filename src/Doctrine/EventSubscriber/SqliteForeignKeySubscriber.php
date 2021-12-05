<?php

namespace App\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Platforms\SqlitePlatform;

class SqliteForeignKeySubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::postConnect,
        ];
    }

    public function postConnect(ConnectionEventArgs $args): void
    {
        $connection = $args->getConnection();

        if ($connection->getDatabasePlatform() instanceof SqlitePlatform) {
            $connection->executeStatement('PRAGMA foreign_keys = ON');
        }
    }
}
