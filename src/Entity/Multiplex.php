<?php

namespace App\Entity;

use App\Entity\Embeddable\RadioStation\Appearance;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Multiplex
{
    use NameableTrait;
    use BroadcastableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: RadioTable::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    private RadioTable $radioTable;

    public function __construct(string $frequency, string $name, RadioTable $radioTable)
    {
        $this->frequency = $frequency;
        $this->name = $name;
        $this->radioTable = $radioTable;

        $this->appearance = new Appearance;
    }

    public function getRadioTable(): RadioTable
    {
        return $this->radioTable;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
