<?php

namespace App\Entity;

use App\Entity\Embeddable\RadioStation\Locality;
use App\Entity\Embeddable\RadioStation\Rds;
use App\Validator\ClassConstantsChoice;
use App\Validator\DabChannel;
use App\Validator\YearMonthDate;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="idx_sort_frequency", columns={"radioTableId", "frequency"}),
 *     @ORM\Index(name="idx_sort_name", columns={"radioTableId", "name", "frequency"}),
 * })
 * @ORM\Entity(repositoryClass="App\Repository\RadioStationRepository")
 * @ORM\EntityListeners({"App\Doctrine\EntityListener\RadioStationListener"})
 */
class RadioStation
{
    public const POLARIZATION_HORIZONTAL = 'H';
    public const POLARIZATION_VERTICAL = 'V';
    public const POLARIZATION_CIRCULAR = 'C';
    public const POLARIZATION_VARIOUS = 'M';
    public const POLARIZATION_NONE = null;

    public const RECEPTION_REGULAR = 0;
    public const RECEPTION_TROPO = 1;
    public const RECEPTION_SCATTER = 2;
    public const RECEPTION_SPORADIC_E = 3;

    public const QUALITY_VERY_GOOD = 5;
    public const QUALITY_GOOD = 4;
    public const QUALITY_MIDDLE = 3;
    public const QUALITY_BAD = 2;
    public const QUALITY_VERY_BAD = 1;

    public const TYPE_MUSIC = 1;
    public const TYPE_INFORMATION = 2;
    public const TYPE_UNIVERSAL = 3;
    public const TYPE_RELIGIOUS = 4;
    public const TYPE_OTHER = 0;

    /** @deprecated */
    public const MARKER_1 = 1;
    /** @deprecated */
    public const MARKER_2 = 2;
    /** @deprecated */
    public const MARKER_3 = 3;
    /** @deprecated */
    public const MARKER_4 = 4;
    /** @deprecated */
    public const MARKER_5 = 5;
    /** @deprecated */
    public const MARKER_6 = 6;
    /** @deprecated */
    public const MARKER_NONE = null;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RadioTable")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $radioTable;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="radio_station.name.not_blank")
     * @Assert\Length(max=100, maxMessage="radio_station.name.max_length")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=50, maxMessage="radio_station.radio_group.max_length")
     */
    private $radioGroup;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=50, maxMessage="radio_station.country.max_length")
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(max=50, maxMessage="radio_station.region.max_length")
     */
    private $region;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=3)
     * @Assert\NotBlank(message="radio_station.frequency.not_blank")
     * @Assert\Type("numeric")
     * @Assert\GreaterThan(0, message="radio_station.frequency.greater_than_zero")
     */
    private $frequency;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(max=100, maxMessage="radio_station.location.max_length")
     */
    private $location;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=3, nullable=true)
     * @Assert\Type("numeric")
     * @Assert\GreaterThan(0, message="radio_station.power.greater_than_zero")
     */
    private $power;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     * @ClassConstantsChoice(class=RadioStation::class, prefix="POLARIZATION_")
     */
    private $polarization = self::POLARIZATION_NONE;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=100, maxMessage="radio_station.multiplex.max_length")
     */
    private $multiplex;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @DabChannel(message="radio_station.dab_channel.invalid_for_frequency")
     */
    private $dabChannel;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\GreaterThan(0, message="radio_station.distance.greater_than_zero")
     */
    private $distance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxSignalLevel;

    /**
     * @ORM\Column(type="smallint")
     * @ClassConstantsChoice(class=RadioStation::class, prefix="RECEPTION_")
     */
    private $reception = self::RECEPTION_REGULAR;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type("int")
     * @Assert\GreaterThan(0, message="radio_station.private_number.greater_than_zero")
     */
    private $privateNumber;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @YearMonthDate(message="radio_station.first_log_date.invalid_format")
     */
    private $firstLogDate;

    /**
     * @ORM\Column(type="smallint")
     * @ClassConstantsChoice(class=RadioStation::class, prefix="QUALITY_")
     */
    private $quality = self::QUALITY_VERY_GOOD;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @ClassConstantsChoice(class=RadioStation::class, prefix="MARKER_")
     */
    private $marker = self::MARKER_NONE;

    /**
     * @ORM\Column(type="smallint")
     * @ClassConstantsChoice(class=RadioStation::class, prefix="TYPE_")
     */
    private $type = self::TYPE_MUSIC;

    /**
     * @ORM\Embedded(class=Locality::class)
     */
    private $locality;

    /**
     * @ORM\Embedded(class=Rds::class)
     * @Assert\Valid
     */
    private $rds;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     * @Assert\Length(max=500, maxMessage="radio_station.comment.max_length")
     */
    private $comment;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     * @Assert\Length(max=500, maxMessage="radio_station.external_anchor.max_length")
     * @Assert\Url(message="radio_station.external_anchor.invalid_format")
     */
    private $externalAnchor;

    public function __construct()
    {
        $this->locality = new Locality;
        $this->rds = new Rds;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRadioTable(): ?RadioTable
    {
        return $this->radioTable;
    }

    public function setRadioTable(RadioTable $radioTable): self
    {
        $this->radioTable = $radioTable;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
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

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

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

    public function getMultiplex(): ?string
    {
        return $this->multiplex;
    }

    public function setMultiplex(?string $multiplex): self
    {
        $this->multiplex = $multiplex;

        return $this;
    }

    public function getDabChannel(): ?string
    {
        return $this->dabChannel;
    }

    public function setDabChannel(?string $dabChannel): self
    {
        $this->dabChannel = $dabChannel;

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

    public function getReception(): ?int
    {
        return $this->reception;
    }

    public function setReception(?int $reception): self
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

    /**
     * @todo Remove it in next release, after migration.
     * @deprecated
     */
    public function getMarker(): ?int
    {
        @trigger_error(sprintf('%1$s::%2$s() is deprecated, use %1$s::%3$s() instead.', self::class, __FUNCTION__, 'getAppearance'), E_USER_DEPRECATED);

        return $this->marker;
    }

    /**
     * @todo Remove it in next release, after migration.
     * @deprecated
     */
    public function setMarker(?int $marker): self
    {
        @trigger_error(sprintf('%1$s::%2$s() is deprecated, use %1$s::%3$s() instead.', self::class, __FUNCTION__, 'setAppearance'), E_USER_DEPRECATED);

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

    /**
     * @todo Remove it in next release, after migration.
     * @deprecated
     */
    public function getLocality(): Locality
    {
        @trigger_error(sprintf('%1$s::%2$s() is deprecated, do not use it.', self::class, __FUNCTION__), E_USER_DEPRECATED);

        return $this->locality;
    }

    public function getRds(): Rds
    {
        return $this->rds;
    }

    /**
     * Each radio table column has its getter, by design.
     */
    public function getRdsPi(): ?string
    {
        return $this->rds->getPi();
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

    public function getExternalAnchor(): ?string
    {
        return $this->externalAnchor;
    }

    public function setExternalAnchor(?string $externalAnchor): self
    {
        $this->externalAnchor = $externalAnchor;

        return $this;
    }
}
