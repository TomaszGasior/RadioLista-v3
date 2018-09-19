<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(indexes={
       @ORM\Index(name="SortByFrequency", columns={"id", "frequency"}),
       @ORM\Index(name="SortByName", columns={"id", "name"}),
   })
 * @ORM\Entity(repositoryClass="App\Repository\RadioStationRepository")
 * @ORM\EntityListeners({"App\EventListener\EntityListener\RadioStationListener"})
 */
class RadioStation
{
    public const POLARIZATION_HORIZONTAL = 'H';
    public const POLARIZATION_VERTICAL   = 'V';
    public const POLARIZATION_CIRCULAR   = 'C';
    public const POLARIZATION_VARIOUS    = 'M';
    public const POLARIZATION_NONE       = '';

    public const QUALITY_VERY_GOOD = 5;
    public const QUALITY_GOOD      = 4;
    public const QUALITY_MIDDLE    = 3;
    public const QUALITY_BAD       = 2;
    public const QUALITY_VERY_BAD  = 1;

    public const TYPE_MUSIC       = 1;
    public const TYPE_INFORMATION = 2;
    public const TYPE_UNIVERSAL   = 3;
    public const TYPE_RELIGIOUS   = 4;
    public const TYPE_OTHER       = 0;

    public const LOCALITY_COUNTRY = 1;
    public const LOCALITY_LOCAL   = 2;
    public const LOCALITY_NETWORK = 3;

    public const MARKER_1    = 1;
    public const MARKER_2    = 2;
    public const MARKER_3    = 3;
    public const MARKER_4    = 4;
    public const MARKER_5    = 5;
    public const MARKER_6    = 6;
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
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private $radioGroup;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=50)
     */
    private $country;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type("numeric")
     */
    private $frequency;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(max=100)
     */
    private $location;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true)
     * @Assert\Type("numeric")
     */
    private $power;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     * @Assert\Choice({
     *     RadioStation::POLARIZATION_HORIZONTAL,
     *     RadioStation::POLARIZATION_VERTICAL,
     *     RadioStation::POLARIZATION_CIRCULAR,
     *     RadioStation::POLARIZATION_VARIOUS,
     *     RadioStation::POLARIZATION_NONE,
     * })
     */
    private $polarization = self::POLARIZATION_NONE;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type("int")
     */
    private $privateNumber;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Choice({
     *     RadioStation::QUALITY_VERY_GOOD,
     *     RadioStation::QUALITY_GOOD,
     *     RadioStation::QUALITY_MIDDLE,
     *     RadioStation::QUALITY_BAD,
     *     RadioStation::QUALITY_VERY_BAD,
     * })
     */
    private $quality = self::QUALITY_VERY_GOOD;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Choice({
     *     RadioStation::MARKER_1,
     *     RadioStation::MARKER_2,
     *     RadioStation::MARKER_3,
     *     RadioStation::MARKER_4,
     *     RadioStation::MARKER_5,
     *     RadioStation::MARKER_6,
     *     RadioStation::MARKER_NONE,
     * })
     */
    private $marker = self::MARKER_NONE;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Choice({
     *     RadioStation::TYPE_MUSIC,
     *     RadioStation::TYPE_INFORMATION,
     *     RadioStation::TYPE_UNIVERSAL,
     *     RadioStation::TYPE_RELIGIOUS,
     *     RadioStation::TYPE_OTHER,
     * })
     */
    private $type = self::TYPE_MUSIC;

    /**
     * @ORM\Column(type="array")
     * @Assert\Collection(fields = {
     *     "type" = @Assert\Choice({
     *         RadioStation::LOCALITY_COUNTRY,
     *         RadioStation::LOCALITY_LOCAL,
     *         RadioStation::LOCALITY_NETWORK,
     *     }),
     *     "city" = @Assert\Type("text"),
     * })
     */
    private $locality = [
        'type' => self::LOCALITY_COUNTRY,
        'city' => '',
    ];

    /**
     * @ORM\Column(type="array")
     * @Assert\Collection(fields = {
     *     "rt"  = @Assert\Type("text"),
     *     "ps"  = @Assert\Type("text"),
     *     "pty" = @Assert\Type("text"),
     * })
     */
    private $rds = [
        'rt'  => '',
        'ps'  => '',
        'pty' => '',
    ];

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     * @Assert\Length(max=500)
     */
    private $comment;

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
