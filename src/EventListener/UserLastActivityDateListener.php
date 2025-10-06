<?php

namespace App\EventListener;

use App\Entity\User;
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

    #[AsEventListener(RadioTableCreated::class)]
    #[AsEventListener(RadioTableUpdated::class)]
    #[AsEventListener(RadioTableRemoved::class)]
    public function onRadioTableChanged(RadioTableCreated|RadioTableUpdated|RadioTableRemoved $event): void
    {
        $user = $event->radioTable->getOwner();

        if (!$this->isCurrentUser($user)) {
            return;
        }

        $user->refreshLastActivityDate();
    }

    #[AsEventListener(RadioStationCreated::class)]
    #[AsEventListener(RadioStationUpdated::class)]
    #[AsEventListener(RadioStationRemoved::class)]
    public function onRadioStationChanged(RadioStationCreated|RadioStationUpdated|RadioStationRemoved $event): void
    {
        $user = $event->radioStation->getRadioTable()->getOwner();

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
