<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="RadioTables")
 * @ORM\Entity(repositoryClass="App\Repository\RadioTableRepository")
 */
class RadioTable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="radioTables")
     * @ORM\JoinColumn(name="ownerId", nullable=false)
     */
    private $owner;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\Column(type="array")
     */
    private $columns;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $sorting;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="array")
     */
    private $appearance;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastUpdateTime;

    /**
     * @ORM\Column(type="boolean")
     */
    private $useKhz;

    /**
     * @ORM\Column(type="integer")
     */
    private $radioStationsCount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
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
        return $this->appearance;
    }

    public function setAppearance(array $appearance): self
    {
        $this->appearance = $appearance;

        return $this;
    }

    public function getLastUpdateTime(): ?\DateTimeInterface
    {
        return $this->lastUpdateTime;
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
}
