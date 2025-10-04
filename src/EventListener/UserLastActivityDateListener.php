<?php

namespace App\EventListener;

use App\Event\RadioStationCreated;
use App\Event\RadioStationRemoved;
use App\Event\RadioStationUpdated;
use App\Event\RadioTableCreated;
use App\Event\RadioTableRemoved;
use App\Event\RadioTableUpdated;
use App\Event\UserUpdated;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserLastActivityDateListener
{
    public function __construct(
        #[Autowire('@=null !== service("request_stack").getCurrentRequest()')]
        private bool $enableDateTimeRefresh,
    ) {}

    #[AsEventListener]
    public function onUserUpdated(UserUpdated $event): void
    {
        if (!$this->enableDateTimeRefresh) {
            return;
        }

        $user = $event->user;

        if (in_array('aboutMe', $event->changedFields) || in_array('publicProfile', $event->changedFields)) {
            $user->refreshLastActivityDate();
        }
    }

    #[AsEventListener(RadioTableCreated::class)]
    #[AsEventListener(RadioTableUpdated::class)]
    #[AsEventListener(RadioTableRemoved::class)]
    public function onRadioTableChanged(RadioTableCreated|RadioTableUpdated|RadioTableRemoved $event): void
    {
        if (!$this->enableDateTimeRefresh) {
            return;
        }

        $user = $event->radioTable->getOwner();

        $user->refreshLastActivityDate();
    }

    #[AsEventListener(RadioStationCreated::class)]
    #[AsEventListener(RadioStationUpdated::class)]
    #[AsEventListener(RadioStationRemoved::class)]
    public function onRadioStationChanged(RadioStationCreated|RadioStationUpdated|RadioStationRemoved $event): void
    {
        if (!$this->enableDateTimeRefresh) {
            return;
        }

        $user = $event->radioStation->getRadioTable()->getOwner();

        $user->refreshLastActivityDate();
    }
}
