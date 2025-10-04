<?php

namespace App\EventListener;

use App\Event\RadioStationCreated;
use App\Event\RadioStationRemoved;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class RadioStationCountListener
{
    #[AsEventListener]
    public function onRadioStationCreated(RadioStationCreated $event): void
    {
        $radioTable = $event->radioStation->getRadioTable();

        $radioTable->increaseRadioStationsCount();
    }

    #[AsEventListener]
    public function onRadioStationRemoved(RadioStationRemoved $event): void
    {
        $radioTable = $event->radioStation->getRadioTable();

        $radioTable->decreaseRadioStationsCount();
    }
}
