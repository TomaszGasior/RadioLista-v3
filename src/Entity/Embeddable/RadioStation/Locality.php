<?php

namespace App\Entity\Embeddable\RadioStation;

use App\Validator\ClassConstantsChoice;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class Locality
{
    public const TYPE_COUNTRY = 1;
    public const TYPE_LOCAL = 2;
    public const TYPE_NETWORK = 3;

    /**
     * @ORM\Column(type="smallint")
     * @ClassConstantsChoice(class=Locality::class, prefix="TYPE_")
     */
    private $type = self::TYPE_COUNTRY;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(max=100, maxMessage="radio_station.locality_city.max_length")
     */
    private $city;

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }
}
