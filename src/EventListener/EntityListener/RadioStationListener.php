<?php

namespace App\EventListener\EntityListener;

use App\Entity\RadioStation;
use Doctrine\Common\EventArgs;
use Doctrine\ORM\Mapping\PreFlush;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreRemove;

class RadioStationListener extends AbstractEntityListener
{
    /**
     * @PreFlush
     * @PreRemove
     */
    public function refreshLastUpdateTimeOfRadioTable(RadioStation $radioStation, EventArgs $args): void
    {
        $radioTable = $radioStation->getRadioTable();

        $radioTable->refreshLastUpdateTime();
        $this->forceEntityUpdate($radioTable, $args);
    }

    /**
     * @PrePersist
     */
    public function increaseRadioStationsCountOfRadioTable(RadioStation $radioStation, EventArgs $args): void
    {
        $radioTable = $radioStation->getRadioTable();

        $radioTable->increaseRadioStationsCount();
        $this->forceEntityUpdate($radioTable, $args);
    }

    /**
     * @PreRemove
     */
    public function decreaseRadioStationsCountOfRadioTable(RadioStation $radioStation, EventArgs $args): void
    {
        $radioTable = $radioStation->getRadioTable();

        $radioTable->decreaseRadioStationsCount();
        $this->forceEntityUpdate($radioTable, $args);
    }
}
