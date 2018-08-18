<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="RadioStations")
 * @ORM\Entity(repositoryClass="App\Repository\RadioStationRepository")
 */
class RadioStation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $radioTableId;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $radioGroup;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     */
    private $frequency;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $location;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true)
     */
    private $power;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $polarization;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $privateNumber;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $quality;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $marker;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="array")
     */
    private $locality;

    /**
     * @ORM\Column(type="array")
     */
    private $rds;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $comment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRadioTableId(): ?int
    {
        return $this->radioTableId;
    }

    public function setRadioTableId(int $radioTableId): self
    {
        $this->radioTableId = $radioTableId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRadioGroup(): ?string
    {
        return $this->radioGroup;
    }

    public function setRadioGroup(?string $radioGroup): self
    {
        $this->radioGroup = $radioGroup;

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

    public function getFrequency()
    {
        return $this->frequency;
    }

    public function setFrequency($frequency): self
    {
        $this->frequency = $frequency;

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

    public function getPower()
    {
        return $this->power;
    }

    public function setPower($power): self
    {
        $this->power = $power;

        return $this;
    }

    public function getPolarization(): ?string
    {
        return $this->polarization;
    }

    public function setPolarization(?string $polarization): self
    {
        $this->polarization = $polarization;

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

    public function getQuality(): ?int
    {
        return $this->quality;
    }

    public function setQuality(?int $quality): self
    {
        $this->quality = $quality;

        return $this;
    }

    public function getMarker(): ?int
    {
        return $this->marker;
    }

    public function setMarker(?int $marker): self
    {
        $this->marker = $marker;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLocality(): ?array
    {
        return $this->locality;
    }

    public function setLocality(array $locality): self
    {
        $this->locality = $locality;

        return $this;
    }

    public function getRds(): ?array
    {
        return $this->rds;
    }

    public function setRds(array $rds): self
    {
        $this->rds = $rds;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
