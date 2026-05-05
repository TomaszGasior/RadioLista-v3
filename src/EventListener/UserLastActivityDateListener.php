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
use App\Event\RadioTableCreated;
use App\Event\RadioTableRemoved;
use App\Event\RadioTableUpdated;
use App\Event\UserUpdated;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class UserLastActivityDateListener
{
    public function __construct(private Security $security) {}

    #[AsEventListener]
    public function onUserUpdated(UserUpdated $event): void
    {
        $user = $event->user;

        if (!$this->isCurrentUser($user)) {
            return;
        }

        if (in_array('aboutMe', $event->changedFields) || in_array('publicProfile', $event->changedFields)) {
            $user->refreshLastActivityDate();
        }
    }

    #[AsEventListener]
    public function onRadioTableChanged(RadioTableCreated|RadioTableUpdated|RadioTableRemoved $event): void
    {
        $user = $event->radioTable->getOwner();

        if (!$this->isCurrentUser($user)) {
            return;
        }

        $user->refreshLastActivityDate();
    }

    #[AsEventListener]
    public function onRadioStationChanged(RadioStationCreated|RadioStationUpdated|RadioStationRemoved $event): void
    {
        $user = $event->radioStation->getRadioTable()->getOwner();

        if (!$this->isCurrentUser($user)) {
            return;
        }

        $user->refreshLastActivityDate();
    }

    #[AsEventListener]
    public function onDigitalRadioStationChanged(DigitalRadioStationCreated|DigitalRadioStationUpdated|DigitalRadioStationRemoved $event): void
    {
        $user = $event->digitalRadioStation->getMultiplex()->getRadioTable()->getOwner();

        if (!$this->isCurrentUser($user)) {
            return;
        }

        $user->refreshLastActivityDate();
    }

    #[AsEventListener]
    public function onMultiplexChanged(MultiplexCreated|MultiplexUpdated|MultiplexRemoved $event): void
    {
        $user = $event->multiplex->getRadioTable()->getOwner();

        if (!$this->isCurrentUser($user)) {
            return;
        }

        $user->refreshLastActivityDate();
    }

    private function isCurrentUser(User $user): bool
    {
        return $user === $this->security->getUser();
    }
}
