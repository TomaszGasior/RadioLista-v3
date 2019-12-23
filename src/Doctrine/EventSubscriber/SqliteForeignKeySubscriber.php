<?php

namespace App\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Platforms\SqlitePlatform;

class SqliteForeignKeySubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::postConnect,
        ];
    }

    public function postConnect(ConnectionEventArgs $args)
    {
        if ($args->getDatabasePlatform() instanceof SqlitePlatform) {
            $args->getConnection()->exec('PRAGMA foreign_keys = ON');
        }
    }
}
