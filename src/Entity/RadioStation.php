<?php

namespace App\Entity;

use App\Entity\Embeddable\RadioStation\Appearance;
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

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RadioTable")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private RadioTable $radioTable;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private ?string $radioGroup = null;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private ?string $country = null;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(max=50)
     */
    private ?string $region = null;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=3)
     * @Assert\NotBlank()
     * @Assert\Type("numeric")
     * @Assert\GreaterThan(0)
     */
    private ?string $frequency = null;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(max=100)
     */
    private ?string $location = null;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=3, nullable=true)
     * @Assert\Type("numeric")
     * @Assert\GreaterThan(0)
     */
    private ?string $power = null;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     * @ClassConstantsChoice(class=RadioStation::class, prefix="POLARIZATION_")
     */
    private ?string $polarization = self::POLARIZATION_NONE;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=100)
     */
    private ?string $multiplex = null;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @DabChannel()
     */
    private ?string $dabChannel = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\GreaterThan(0)
     */
    private ?int $distance = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $maxSignalLevel = null;

    /**
     * @ORM\Column(type="smallint")
     * @ClassConstantsChoice(class=RadioStation::class, prefix="RECEPTION_")
     */
    private int $reception = self::RECEPTION_REGULAR;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type("int")
     * @Assert\GreaterThan(0)
     */
    private ?int $privateNumber = null;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @YearMonthDate()
     */
    private ?string $firstLogDate = null;

    /**
     * @ORM\Column(type="smallint")
     * @ClassConstantsChoice(class=RadioStation::class, prefix="QUALITY_")
     */
    private int $quality = self::QUALITY_VERY_GOOD;

    /**
     * @ORM\Column(type="smallint")
     * @ClassConstantsChoice(class=RadioStation::class, prefix="TYPE_")
     */
    private int $type = self::TYPE_MUSIC;

    /**
     * @ORM\Embedded(class=Rds::class)
     * @Assert\Valid
     */
    private Rds $rds;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     * @Assert\Length(max=500)
     */
    private ?string $comment = null;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     * @Assert\Length(max=500)
     * @Assert\Url()
     */
    private ?string $externalAnchor = null;

    /**
     * @ORM\Embedded(class=Appearance::class)
     * @Assert\Valid
     */
    private Appearance $appearance;

    public function __construct()
    {
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

    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    public function setFrequency(?string $frequency): self
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

    public function getPower(): ?string
    {
        return $this->power;
    }

    public function setPower(?string $power): self
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

    public function getReception(): int
    {
        return $this->reception;
    }

    public function setReception(int $reception): self
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

    public function getQuality(): int
    {
        return $this->quality;
    }

    public function setQuality(int $quality): self
    {
        $this->quality = $quality;

        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getAppearance(): Appearance
    {
        return $this->appearance;
    }
}
