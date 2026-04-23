<?php

namespace App\Entity;

use App\Entity\Enum\RadioStation\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait RadioStationTrait
{
    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $radioGroup = null;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $region = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Assert\Type('int')]
    #[Assert\GreaterThan(0)]
    private ?int $privateNumber = null;

    #[ORM\Column(type: Types::SMALLINT, enumType: Type::class)]
    private Type $type = Type::MUSIC;

    public function getRadioGroup(): ?string
    {
        return $this->radioGroup;
    }

    public function setRadioGroup(?string $radioGroup): self
    {
        $this->radioGroup = $radioGroup;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

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
}
