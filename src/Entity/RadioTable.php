<?php

namespace App\Entity;

use App\Doctrine\EntityListener\RadioTableListener;
use App\Entity\Embeddable\RadioTable\Appearance;
use App\Entity\Enum\RadioTable\Column;
use App\Entity\Enum\RadioTable\DistanceUnit;
use App\Entity\Enum\RadioTable\FrequencyUnit;
use App\Entity\Enum\RadioTable\MaxSignalLevelUnit;
use App\Entity\Enum\RadioTable\PowerUnit;
use App\Entity\Enum\RadioTable\Status;
use App\Repository\RadioTableRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RadioTableRepository::class)]
#[ORM\EntityListeners([RadioTableListener::class])]
#[ORM\Cache('NONSTRICT_READ_WRITE')]
#[ORM\Index(name: 'idx_status', columns: ['status'])]
#[ORM\Index(name: 'idx_search_term', columns: ['name', 'description'], flags: ['fulltext'])]
class RadioTable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    private ?User $owner = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT, enumType: Status::class)]
    private Status $status = Status::PUBLIC;

    #[ORM\Column(type: Types::JSON, enumType: Column::class)]
    #[Assert\Expression(
        'frequency in value && name in value',
        values: ['frequency' => Column::FREQUENCY, 'name' => Column::NAME]
    )]
    private array $columns = [
        Column::FREQUENCY,
        Column::NAME,
        Column::LOCATION,
        Column::POWER,
        Column::POLARIZATION,
        Column::COUNTRY,
        Column::QUALITY,
        Column::RDS,
    ];

    #[ORM\Column(type: Types::STRING, length: 15, enumType: Column::class)]
    private Column $sorting = Column::FREQUENCY;

    #[ORM\Column(type: Types::STRING, length: 2000, nullable: true)]
    #[Assert\Length(max: 2000)]
    private ?string $description = null;

    #[ORM\Embedded(class: Appearance::class)]
    #[Assert\Valid]
    private Appearance $appearance;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $creationTime;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $lastUpdateTime;

    #[ORM\Column(type: Types::SMALLINT, enumType: FrequencyUnit::class)]
    private FrequencyUnit $frequencyUnit = FrequencyUnit::MHZ;

    #[ORM\Column(type: Types::SMALLINT, enumType: MaxSignalLevelUnit::class)]
    private MaxSignalLevelUnit $maxSignalLevelUnit = MaxSignalLevelUnit::DBF;

    #[ORM\Column(type: Types::INTEGER)]
    private int $radioStationsCount = 0;

    public function __construct()
    {
        $this->appearance = new Appearance;
        $this->creationTime = new DateTime;
        $this->lastUpdateTime = new DateTime;
    }

    public function __clone()
    {
        $this->appearance = clone $this->appearance;
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

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param Column[] $columns
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function getSorting(): Column
    {
        return $this->sorting;
    }

    public function setSorting(Column $sorting): self
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
    public function getCreationTime(): ?DateTimeInterface
    {
        return $this->creationTime;
    }

    public function getLastUpdateTime(): DateTimeInterface
    {
        return $this->lastUpdateTime;
    }

    public function refreshLastUpdateTime(): self
    {
        $this->lastUpdateTime = new DateTime;

        return $this;
    }

    public function getFrequencyUnit(): FrequencyUnit
    {
        return $this->frequencyUnit;
    }

    public function setFrequencyUnit(FrequencyUnit $frequencyUnit): self
    {
        $this->frequencyUnit = $frequencyUnit;

        return $this;
    }

    public function getMaxSignalLevelUnit(): MaxSignalLevelUnit
    {
        return $this->maxSignalLevelUnit;
    }

    public function setMaxSignalLevelUnit(MaxSignalLevelUnit $maxSignalLevelUnit): self
    {
        $this->maxSignalLevelUnit = $maxSignalLevelUnit;

        return $this;
    }

    public function getRadioStationsCount(): int
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

    public function getPowerUnit(): PowerUnit
    {
        return PowerUnit::KW;
    }

    public function getDistanceUnit(): DistanceUnit
    {
        return DistanceUnit::KM;
    }
}
