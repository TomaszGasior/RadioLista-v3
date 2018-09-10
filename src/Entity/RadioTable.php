<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(indexes={
       @ORM\Index(name="Search", columns={"name", "description"}, flags={"fulltext"}),
   })
 * @ORM\Entity(repositoryClass="App\Repository\RadioTableRepository")
 */
class RadioTable
{
    public const SORTING_FREQUENCY      = 'frequency';
    public const SORTING_NAME           = 'name';
    public const SORTING_PRIVATE_NUMBER = 'privateNumber';

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
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status = self::STATUS_PUBLIC;

    /**
     * @ORM\Column(type="array")
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
     */
    private $sorting = self::SORTING_FREQUENCY;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="array")
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
