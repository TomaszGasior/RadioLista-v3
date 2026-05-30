<?php

namespace App\Entity;

use App\DigitalMigration\EntityTrait\DigitalPropertiesSetterMigrationTrait;
use App\Entity\Embeddable\RadioStation\Appearance;
use App\Entity\Embeddable\RadioStation\Rds;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class DigitalRadioStation
{
    use NameableTrait;
    use RadioStationTrait;
    use DigitalPropertiesSetterMigrationTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Multiplex::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    private Multiplex $multiplex;

    public function __construct(string $name, Multiplex $multiplex)
    {
        $this->name = $name;
        $this->multiplex = $multiplex;

        $this->rds = new Rds;
        $this->appearance = new Appearance;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMultiplex(): Multiplex
    {
        return $this->multiplex;
    }
}
