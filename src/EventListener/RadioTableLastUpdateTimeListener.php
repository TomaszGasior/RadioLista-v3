<?php

namespace App\EventListener;

use App\Event\RadioStationCreated;
use App\Event\RadioStationRemoved;
use App\Event\RadioStationUpdated;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class RadioTableLastUpdateTimeListener
{
    public function __construct(
        #[Autowire('@=null !== service("request_stack").getCurrentRequest()')]
        private bool $enableDateTimeRefresh,
    ) {}

    #[AsEventListener(RadioStationCreated::class)]
    #[AsEventListener(RadioStationUpdated::class)]
    #[AsEventListener(RadioStationRemoved::class)]
    public function onRadioStationChanged(RadioStationCreated|RadioStationUpdated|RadioStationRemoved $event): void
    {
        if (!$this->enableDateTimeRefresh) {
            return;
        }

        $radioTable = $event->radioStation->getRadioTable();

        $radioTable->refreshLastUpdateTime();
    }
}
