<?php

namespace App\EventListener;

use App\Event\RadioTableCreated;
use App\Event\RadioTableRemoved;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserRadioTableCountListener
{
    #[AsEventListener]
    public function onRadioTableCreated(RadioTableCreated $event): void
    {
        $user = $event->radioTable->getOwner();

        $user->increaseRadioTablesCount();
    }

    #[AsEventListener]
    public function onRadioTableRemoved(RadioTableRemoved $event): void
    {
        $user = $event->radioTable->getOwner();

        $user->decreaseRadioTablesCount();
    }
}
