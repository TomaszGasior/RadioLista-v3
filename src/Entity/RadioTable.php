<?php

namespace App\Entity;

use App\Entity\Embeddable\RadioTable\Appearance;
use App\Validator\ClassConstantsChoice;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="idx_status", columns={"status"}),
 *     @ORM\Index(name="idx_search_term", columns={"name", "description"}, flags={"fulltext"}),
 * })
 * @ORM\Entity(repositoryClass="App\Repository\RadioTableRepository")
 * @ORM\EntityListeners({"App\Doctrine\EntityListener\RadioTableListener"})
 * @ORM\Cache("NONSTRICT_READ_WRITE")
 */
class RadioTable
{
    // Keep these constants and its values in sync with RadioStation's field names.
    // Order of them affects order in disabled columns in radio table settings page. :)
    public const COLUMN_FREQUENCY = 'frequency';
    public const COLUMN_NAME = 'name';
    public const COLUMN_LOCATION = 'location';
    public const COLUMN_POWER = 'power';
    public const COLUMN_POLARIZATION = 'polarization';
    public const COLUMN_MULTIPLEX = 'multiplex';
    public const COLUMN_DAB_CHANNEL = 'dabChannel';
    public const COLUMN_COUNTRY = 'country';
    public const COLUMN_REGION = 'region';
    public const COLUMN_QUALITY = 'quality';
    public const COLUMN_RDS = 'rds';
    public const COLUMN_FIRST_LOG_DATE = 'firstLogDate';
    public const COLUMN_RECEPTION = 'reception';
    public const COLUMN_DISTANCE = 'distance';
    public const COLUMN_MAX_SIGNAL_LEVEL = 'maxSignalLevel';
    public const COLUMN_RDS_PI = 'rdsPi';
    public const COLUMN_RADIO_GROUP = 'radioGroup';
    public const COLUMN_TYPE = 'type';
    public const COLUMN_PRIVATE_NUMBER = 'privateNumber';
    public const COLUMN_COMMENT = 'comment';
    public const COLUMN_EXTERNAL_ANCHOR = 'externalAnchor';

    public const SORTING_FREQUENCY = self::COLUMN_FREQUENCY;
    public const SORTING_NAME = self::COLUMN_NAME;
    public const SORTING_PRIVATE_NUMBER = self::COLUMN_PRIVATE_NUMBER;

    public const STATUS_PUBLIC = 1;
    public const STATUS_UNLISTED = 0;
    public const STATUS_PRIVATE = -1;

    public const FREQUENCY_MHZ = 1;
    public const FREQUENCY_KHZ = 2;

    public const MAX_SIGNAL_LEVEL_DB = 1;
    public const MAX_SIGNAL_LEVEL_DBF = 2;
    public const MAX_SIGNAL_LEVEL_DBUV = 3;
    public const MAX_SIGNAL_LEVEL_DBM = 4;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $owner;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     * @ClassConstantsChoice(class=RadioTable::class, prefix="STATUS_")
     */
    private $status = self::STATUS_PUBLIC;

    /**
     * @ORM\Column(type="array")
     * @ClassConstantsChoice(class=RadioTable::class, prefix="COLUMN_", multiple=true)
     * @Assert\Expression(
     *     "frequency in value && name in value",
     *     values={"frequency"=RadioTable::COLUMN_FREQUENCY, "name"=RadioTable::COLUMN_NAME}
     * )
     */
    private $columns = [
        self::COLUMN_FREQUENCY,
        self::COLUMN_NAME,
        self::COLUMN_LOCATION,
        self::COLUMN_POWER,
        self::COLUMN_POLARIZATION,
        self::COLUMN_COUNTRY,
        self::COLUMN_QUALITY,
        self::COLUMN_RDS,
    ];

    /**
     * @ORM\Column(type="string", length=15)
     * @ClassConstantsChoice(class=RadioTable::class, prefix="SORTING_")
     */
    private $sorting = self::SORTING_FREQUENCY;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     * @Assert\Length(max=2000)
     */
    private $description;

    /**
     * @ORM\Embedded(class=Appearance::class)
     * @Assert\Valid
     */
    private $appearance;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $creationTime;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastUpdateTime;

    /**
     * @ORM\Column(type="smallint")
     * @ClassConstantsChoice(class=RadioTable::class, prefix="FREQUENCY_")
     */
    private $frequencyUnit = self::FREQUENCY_MHZ;

    /**
     * @ORM\Column(type="smallint")
     * @ClassConstantsChoice(class=RadioTable::class, prefix="MAX_SIGNAL_LEVEL_")
     */
    private $maxSignalLevelUnit = self::MAX_SIGNAL_LEVEL_DBF;

    /**
     * @ORM\Column(type="integer")
     */
    private $radioStationsCount = 0;

    public function __construct()
    {
        $this->appearance = new Appearance;
        $this->creationTime = new \DateTime;
        $this->lastUpdateTime = new \DateTime;
    }

    public function __clone()
    {
        $this->appearance = $this->appearance;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getColumns(): ?array
    {
        return $this->columns;
    }

    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function getSorting(): ?string
    {
        return $this->sorting;
    }

    public function setSorting(string $sorting): self
    {
        $this->sorting = $sorting;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAppearance(): Appearance
    {
        return $this->appearance;
    }

    /**
     * Nullable for backward compatibility. Added in 3.14 version.
     */
    public function getCreationTime(): ?\DateTimeInterface
    {
        return $this->creationTime;
    }

    public function getLastUpdateTime(): \DateTimeInterface
    {
        return $this->lastUpdateTime;
    }

    public function refreshLastUpdateTime(): self
    {
        $this->lastUpdateTime = new \DateTime;

        return $this;
    }

    public function getFrequencyUnit(): ?int
    {
        return $this->frequencyUnit;
    }

    public function setFrequencyUnit(int $frequencyUnit): self
    {
        $this->frequencyUnit = $frequencyUnit;

        return $this;
    }

    public function getMaxSignalLevelUnit(): ?int
    {
        return $this->maxSignalLevelUnit;
    }

    public function setMaxSignalLevelUnit(int $maxSignalLevelUnit): self
    {
        $this->maxSignalLevelUnit = $maxSignalLevelUnit;

        return $this;
    }

    public function getRadioStationsCount(): ?int
    {
        return $this->radioStationsCount;
    }

    public function increaseRadioStationsCount(): self
    {
        ++$this->radioStationsCount;

        return $this;
    }

    public function decreaseRadioStationsCount(): self
    {
        --$this->radioStationsCount;

        return $this;
    }
}
