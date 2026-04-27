<?php

namespace App\DigitalMigration;

use App\Entity\Enum\RadioStation\DabChannel;
use Deprecated;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait RadioStationLegacyDigitalPropertiesTrait
{
    /** @deprecated */
    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $multiplex = null;

    /** @deprecated */
    #[ORM\Column(type: Types::STRING, length: 5, enumType: DabChannel::class, nullable: true)]
    private ?DabChannel $dabChannel = null;

    /** @deprecated */
    #[Deprecated]
    public function getMultiplex(): ?string
    {
        return $this->multiplex;
    }

    /** @deprecated */
    #[Deprecated]
    public function setMultiplex(?string $multiplex): self
    {
        $this->multiplex = $multiplex;

        return $this;
    }

    /** @deprecated */
    #[Deprecated]
    public function getDabChannel(): ?DabChannel
    {
        return $this->dabChannel;
    }

    /** @deprecated */
    #[Deprecated]
    public function setDabChannel(?DabChannel $dabChannel): self
    {
        $this->dabChannel = $dabChannel;

        return $this;
    }
}
