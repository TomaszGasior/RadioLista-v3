<?php

namespace App\EventListener;

use App\Entity\RadioStation;
use Doctrine\ORM\Mapping\PreFlush;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreRemove;

class RadioStationListener
{
    /**
     * @PreFlush
     * @PreRemove
     */
    public function refreshLastUpdateTimeOfRadioTable(RadioStation $radioStation)
    {
        $radioStation->getRadioTable()->refreshLastUpdateTime();
    }

    /**
     * @PrePersist
     */
    public function increaseRadioStationsCountOfRadioTable(RadioStation $radioStation)
    {
        $radioStation->getRadioTable()->increaseRadioStationsCount();
    }

    /**
     * @PreRemove
     */
    public function decreaseRadioStationsCountOfRadioTable(RadioStation $radioStation)
    {
        $radioStation->getRadioTable()->decreaseRadioStationsCount();
    }
}
