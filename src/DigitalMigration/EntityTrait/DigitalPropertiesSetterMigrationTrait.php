<?php

namespace App\DigitalMigration\EntityTrait;

use App\Entity\Embeddable\RadioStation\Appearance;

/** @deprecated */
trait DigitalPropertiesSetterMigrationTrait
{
    /** @deprecated */
    public function setAppearance(Appearance $apperarance): void
    {
        $this->appearance = clone $apperarance;
    }
}
