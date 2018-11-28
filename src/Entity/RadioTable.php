<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(indexes={
       @ORM\Index(name="Search", columns={"name", "description"}, flags={"fulltext"}),
   })
 * @ORM\Entity(repositoryClass="App\Repository\RadioTableRepository")
 * @ORM\EntityListeners({"App\EventListener\EntityListener\RadioTableListener"})
 */
class RadioTable
{
    public const COLUMN_FREQUENCY      = 'frequency';
    public const COLUMN_PRIVATE_NUMBER = 'privateNumber';
    public const COLUMN_NAME           = 'name';
    public const COLUMN_RADIO_GROUP    = 'radioGroup';
    public const COLUMN_COUNTRY        = 'country';
    public const COLUMN_LOCATION       = 'location';
    public const COLUMN_POWER          = 'power';
    public const COLUMN_POLARIZATION   = 'polarization';
    public const COLUMN_TYPE           = 'type';
    public const COLUMN_LOCALITY       = 'locality';
    public const COLUMN_QUALITY        = 'quality';
    public const COLUMN_RDS            = 'rds';
    public const COLUMN_COMMENT        = 'comment';
    // Keep in sync with RadioStation's fields names.

    public const SORTING_FREQUENCY      = self::COLUMN_FREQUENCY;
    public const SORTING_NAME           = self::COLUMN_NAME;
    public const SORTING_PRIVATE_NUMBER = self::COLUMN_PRIVATE_NUMBER;

    public const STATUS_PUBLIC   = 1;
    public const STATUS_UNLISTED = 0;
    public const STATUS_PRIVATE  = -1;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="radioTables")
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
     * @Assert\Choice({
     *     RadioTable::STATUS_PUBLIC,
     *     RadioTable::STATUS_UNLISTED,
     *     RadioTable::STATUS_PRIVATE,
     * })
     */
    private $status = self::STATUS_PUBLIC;

    /**
     * @ORM\Column(type="array")
     * @Assert\Collection(fields = {
     *     "frequency"     = {
     *         @Assert\Type("int"),
     *         @Assert\GreaterThan(0, message="Częstotliwość musi być widoczna."),
     *     },
     *     "privateNumber" = @Assert\Type("int"),
     *     "name"          = {
     *         @Assert\Type("int"),
     *         @Assert\GreaterThan(0, message="Nazwa musi być widoczna."),
     *     },
     *     "radioGroup"    = @Assert\Type("int"),
     *     "country"       = @Assert\Type("int"),
     *     "location"      = @Assert\Type("int"),
     *     "power"         = @Assert\Type("int"),
     *     "polarization"  = @Assert\Type("int"),
     *     "type"          = @Assert\Type("int"),
     *     "locality"      = @Assert\Type("int"),
     *     "quality"       = @Assert\Type("int"),
     *     "rds"           = @Assert\Type("int"),
     *     "comment"       = @Assert\Type("int"),
     * })
     */
    private $columns = [
        'frequency'     =>  1,
        'privateNumber' => -2,
        'name'          =>  3,
        'radioGroup'    =>  4,
        'country'       => -5,
        'location'      =>  6,
        'power'         => -7,
        'polarization'  => -8,
        'type'          =>  9,
        'locality'      =>  10,
        'quality'       =>  11,
        'rds'           =>  12,
        'comment'       => -13,
    ];

    /**
     * @ORM\Column(type="string", length=15)
     * @Assert\Choice({
     *     RadioTable::SORTING_FREQUENCY,
     *     RadioTable::SORTING_NAME,
     *     RadioTable::SORTING_PRIVATE_NUMBER,
     * })
     */
    private $sorting = self::SORTING_FREQUENCY;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     * @Assert\Length(max=1000)
     */
    private $description;

    /**
     * @ORM\Column(type="array")
     * @Assert\Collection(fields = {
     *     "th"   = @Assert\Type("string"),
     *     "bg"   = @Assert\Type("string"),
     *     "fg"   = @Assert\Type("string"),
     *     "img"  = @Assert\Type("string"),
     *     "full" = @Assert\Type("bool"),
     * })
     */
    private $appearance = [
        'th'   => '',
        'bg'   => '',
        'fg'   => '',
        'img'  => '',
        'full' => false,
    ];

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastUpdateTime;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $useKhz = false;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $radioStationsCount = 0;

    public function __construct()
    {
        $this->lastUpdateTime = new \DateTime;
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

    public function setName(string $name): self
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

    public function getAppearance(): ?array
    {
        // Dirty hack used for backward compatibility with radiotables created in RLv1,
        // improperly ported to RLv2. Sometimes "appearance" array is not filled in
        // with proper default keys. Fix it dynamically to avoid "notice" warnings.
        $fallback = array_fill_keys(['th', 'bg', 'fg', 'img', 'full'], null);

        return $this->appearance + $fallback;
    }

    public function setAppearance(array $appearance): self
    {
        $this->appearance = $appearance;

        return $this;
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

    public function getUseKhz(): ?bool
    {
        return $this->useKhz;
    }

    public function setUseKhz(bool $useKhz): self
    {
        $this->useKhz = $useKhz;

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
