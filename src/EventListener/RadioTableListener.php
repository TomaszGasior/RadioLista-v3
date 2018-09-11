<?php

namespace App\EventListener;

use App\Entity\RadioTable;
use Doctrine\ORM\Mapping\PreFlush;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreRemove;

class RadioTableListener
{
    /**
     * @PreFlush
     * @PreRemove
     */
    public function refreshLastActivityDateOfUser(RadioTable $radioTable)
    {
        $radioTable->getOwner()->refreshLastActivityDate();
    }

    /**
     * @PrePersist
     */
    public function increaseRadioTablesCountOfUser(RadioTable $radioTable)
    {
        $radioTable->getOwner()->increaseRadioTablesCount();
    }

    /**
     * @PreRemove
     */
    public function decreaseRadioTablesCountOfUser(RadioTable $radioTable)
    {
        $radioTable->getOwner()->decreaseRadioTablesCount();
    }
}
