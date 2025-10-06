<?php

namespace App\EventListener;

use App\Entity\User;
use App\Event\RadioStationCreated;
use App\Event\RadioStationRemoved;
use App\Event\RadioStationUpdated;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class RadioTableLastUpdateTimeListener
{
    public function __construct(private Security $security) {}

    #[AsEventListener(RadioStationCreated::class)]
    #[AsEventListener(RadioStationUpdated::class)]
    #[AsEventListener(RadioStationRemoved::class)]
    public function onRadioStationChanged(RadioStationCreated|RadioStationUpdated|RadioStationRemoved $event): void
    {
        $radioTable = $event->radioStation->getRadioTable();
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
