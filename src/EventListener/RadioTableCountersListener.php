<?php

namespace App\EventListener;

use App\Event\DigitalRadioStationCreated;
use App\Event\DigitalRadioStationRemoved;
use App\Event\MultiplexCreated;
use App\Event\MultiplexRemoved;
use App\Event\RadioStationCreated;
use App\Event\RadioStationRemoved;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class RadioTableCountersListener
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

    #[AsEventListener]
    public function onDigitalRadioStationCreated(DigitalRadioStationCreated $event): void
    {
        $radioTable = $event->digitalRadioStation->getMultiplex()->getRadioTable();

        $radioTable->increaseDigitalRadioStationsCount();
    }

    #[AsEventListener]
    public function onDigitalRadioStationRemoved(DigitalRadioStationRemoved $event): void
    {
        $radioTable = $event->digitalRadioStation->getMultiplex()->getRadioTable();

        $radioTable->decreaseDigitalRadioStationsCount();
    }

    #[AsEventListener]
    public function onMultiplexCreated(MultiplexCreated $event): void
    {
        $radioTable = $event->multiplex->getRadioTable();

        $radioTable->increaseMultiplexesCount();
    }

    #[AsEventListener]
    public function onMultiplexRemoved(MultiplexRemoved $event): void
    {
        $radioTable = $event->multiplex->getRadioTable();

        $radioTable->decreaseMultiplexesCount();
    }
}
