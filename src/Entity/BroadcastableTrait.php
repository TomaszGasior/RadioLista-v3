<?php

namespace App\Entity;

use App\Entity\Enum\RadioStation\Polarization;
use App\Entity\Enum\RadioStation\Quality;
use App\Entity\Enum\RadioStation\Reception;
use App\Validator\YearMonthDate;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait BroadcastableTrait
{
    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 3)]
    #[Assert\NotBlank]
    #[Assert\Type('numeric')]
    #[Assert\GreaterThan(0)]
    private string $frequency;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $country = null;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    private ?string $location = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 3, nullable: true)]
    #[Assert\Type('numeric')]
    #[Assert\GreaterThan(0)]
    private ?string $power = null;

    #[ORM\Column(type: Types::STRING, length: 1, enumType: Polarization::class, nullable: true)]
    private ?Polarization $polarization = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Assert\GreaterThan(0)]
    private ?int $distance = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $maxSignalLevel = null;

    #[ORM\Column(type: Types::SMALLINT, enumType: Reception::class)]
    private Reception $reception = Reception::REGULAR;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
    #[YearMonthDate]
    private ?string $firstLogDate = null;

    #[ORM\Column(type: Types::SMALLINT, enumType: Quality::class)]
    private Quality $quality = Quality::VERY_GOOD;

    public function getFrequency(): string
    {
        return $this->frequency;
    }

    public function setFrequency(string $frequency): self
    {
        $this->frequency = $frequency;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getPower(): ?string
    {
        return $this->power;
    }

    public function setPower(?string $power): self
    {
        $this->power = $power;

        return $this;
    }

    public function getPolarization(): ?Polarization
    {
        return $this->polarization;
    }

    public function setPolarization(?Polarization $polarization): self
    {
        $this->polarization = $polarization;

        return $this;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(?int $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getMaxSignalLevel(): ?int
    {
        return $this->maxSignalLevel;
    }

    public function setMaxSignalLevel(?int $maxSignalLevel): self
    {
        $this->maxSignalLevel = $maxSignalLevel;

        return $this;
    }

    public function getReception(): Reception
    {
        return $this->reception;
    }

    public function setReception(Reception $reception): self
    {
        $this->reception = $reception;

        return $this;
    }

    public function getFirstLogDate(): ?string
    {
        return $this->firstLogDate;
    }

    public function setFirstLogDate(?string $firstLogDate): self
    {
        $this->firstLogDate = $firstLogDate;

        return $this;
    }

    public function getQuality(): Quality
    {
        return $this->quality;
    }

    public function setQuality(Quality $quality): self
    {
        $this->quality = $quality;

        return $this;
    }
}
