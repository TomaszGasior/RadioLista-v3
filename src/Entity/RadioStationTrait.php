<?php

namespace App\Entity;

use App\Entity\Embeddable\RadioStation\Rds;
use App\Entity\Enum\RadioStation\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait RadioStationTrait
{
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Assert\Type('int')]
    #[Assert\GreaterThan(0)]
    private ?int $privateNumber = null;

    #[ORM\Column(type: Types::SMALLINT, enumType: Type::class)]
    private Type $type = Type::MUSIC;

    #[ORM\Embedded(class: Rds::class)]
    #[Assert\Valid]
    private Rds $rds;

    public function getPrivateNumber(): ?int
    {
        return $this->privateNumber;
    }

    public function setPrivateNumber(?int $privateNumber): self
    {
        $this->privateNumber = $privateNumber;

        return $this;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function setType(Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRds(): Rds
    {
        return $this->rds;
    }
}
