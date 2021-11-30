<?php

namespace App\Entity\Embeddable\RadioStation;

use App\Validator\ClassConstantsChoice;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 *
 * @todo Remove it in next release, after migration.
 * @deprecated
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

    /**
     * @deprecated
     */
    public function getType(): ?int
    {
        @trigger_error(sprintf('%1$s::%2$s() is deprecated, do not use it.', self::class, __FUNCTION__), E_USER_DEPRECATED);

        return $this->type;
    }

    /**
     * @deprecated
     */
    public function setType(?int $type): self
    {
        @trigger_error(sprintf('%1$s::%2$s() is deprecated, do not use it.', self::class, __FUNCTION__), E_USER_DEPRECATED);

        $this->type = $type;

        return $this;
    }

    /**
     * @deprecated
     */
    public function getCity(): ?string
    {
        @trigger_error(sprintf('%1$s::%2$s() is deprecated, do not use it.', self::class, __FUNCTION__), E_USER_DEPRECATED);

        return $this->city;
    }

    /**
     * @deprecated
     */
    public function setCity(?string $city): self
    {
        @trigger_error(sprintf('%1$s::%2$s() is deprecated, do not use it.', self::class, __FUNCTION__), E_USER_DEPRECATED);

        $this->city = $city;

        return $this;
    }
}
