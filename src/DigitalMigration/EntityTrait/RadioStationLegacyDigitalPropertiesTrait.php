<?php

namespace App\DigitalMigration\EntityTrait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/** @deprecated */
trait RadioStationLegacyDigitalPropertiesTrait
{
    /** @deprecated */
    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $multiplex = null;

    /** @deprecated */
    #[ORM\Column(type: Types::STRING, length: 5, nullable: true)]
    private ?string $dabChannel = null;

    /** @deprecated */
    public function getMultiplex(): ?string
    {
        return $this->multiplex;
    }

    /** @deprecated */
    public function setMultiplexToNull(): void
    {
        $this->multiplex = null;
    }

    /** @deprecated */
    public function getDabChannel(): ?string
    {
        return $this->dabChannel;
    }
}
