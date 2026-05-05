<?php

namespace App\EventListener;

use App\Entity\User;
use App\Event\DigitalRadioStationCreated;
use App\Event\DigitalRadioStationRemoved;
use App\Event\DigitalRadioStationUpdated;
use App\Event\MultiplexCreated;
use App\Event\MultiplexRemoved;
use App\Event\MultiplexUpdated;
use App\Event\RadioStationCreated;
use App\Event\RadioStationRemoved;
use App\Event\RadioStationUpdated;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class RadioTableLastUpdateTimeListener
{
    public function __construct(private Security $security) {}

    #[AsEventListener]
    public function onRadioStationChanged(RadioStationCreated|RadioStationUpdated|RadioStationRemoved $event): void
    {
        $radioTable = $event->radioStation->getRadioTable();
        $user = $radioTable->getOwner();

        if (!$this->isCurrentUser($user)) {
            return;
        }

        $radioTable->refreshLastUpdateTime();
    }

    #[AsEventListener]
    public function onDigitalRadioStationChanged(DigitalRadioStationCreated|DigitalRadioStationUpdated|DigitalRadioStationRemoved $event): void
    {
        $radioTable = $event->digitalRadioStation->getMultiplex()->getRadioTable();
        $user = $radioTable->getOwner();

        if (!$this->isCurrentUser($user)) {
            return;
        }

        $radioTable->refreshLastUpdateTime();
    }

    #[AsEventListener]
    public function onMultiplexChanged(MultiplexCreated|MultiplexUpdated|MultiplexRemoved $event): void
    {
        $radioTable = $event->multiplex->getRadioTable();
        $user = $radioTable->getOwner();

        if (!$this->isCurrentUser($user)) {
            return;
        }

        $radioTable->refreshLastUpdateTime();
    }

    private function isCurrentUser(User $user): bool
    {
        return $user === $this->security->getUser();
    }
}
