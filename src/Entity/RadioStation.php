<?php

namespace App\Entity;

use App\DigitalMigration\RadioStationLegacyDigitalPropertiesTrait;
use App\Entity\Embeddable\RadioStation\Appearance;
use App\Entity\Embeddable\RadioStation\Rds;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Index(name: 'idx_sort_frequency', columns: ['radioTableId', 'frequency'])]
#[ORM\Index(name: 'idx_sort_name', columns: ['radioTableId', 'name', 'frequency'])]
class RadioStation
{
    use NameableTrait;
    use RadioStationTrait;
    use BroadcastableTrait;
    use RadioStationLegacyDigitalPropertiesTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: RadioTable::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    private RadioTable $radioTable;

    #[ORM\Embedded(class: Rds::class)]
    #[Assert\Valid]
    private Rds $rds;

    public function __construct(string $frequency, string $name, RadioTable $radioTable)
    {
        $this->frequency = $frequency;
        $this->name = $name;
        $this->radioTable = $radioTable;

        $this->rds = new Rds;
        $this->appearance = new Appearance;
    }

    public function __clone()
    {
        $this->rds = clone $this->rds;
        $this->appearance = clone $this->appearance;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRadioTable(): RadioTable
    {
        return $this->radioTable;
    }

    public function getRds(): Rds
    {
        return $this->rds;
    }
}
