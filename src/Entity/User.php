<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="Users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $password;

    /**
     * @ORM\Column(type="date")
     */
    private $lastActivityDate;

    /**
     * @ORM\Column(type="date")
     */
    private $registerDate;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $aboutMe;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $publicProfile;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $radioTablesCount;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $oldPassCompat;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getLastActivityDate(): ?\DateTimeInterface
    {
        return $this->lastActivityDate;
    }

    public function setLastActivityDate(\DateTimeInterface $lastActivityDate): self
    {
        $this->lastActivityDate = $lastActivityDate;

        return $this;
    }

    public function getRegisterDate(): ?\DateTimeInterface
    {
        return $this->registerDate;
    }

    public function setRegisterDate(\DateTimeInterface $registerDate): self
    {
        $this->registerDate = $registerDate;

        return $this;
    }

    public function getAboutMe(): ?string
    {
        return $this->aboutMe;
    }

    public function setAboutMe(?string $aboutMe): self
    {
        $this->aboutMe = $aboutMe;

        return $this;
    }

    public function getPublicProfile(): ?bool
    {
        return $this->publicProfile;
    }

    public function setPublicProfile(?bool $publicProfile): self
    {
        $this->publicProfile = $publicProfile;

        return $this;
    }

    public function getRadioTablesCount(): ?int
    {
        return $this->radioTablesCount;
    }

    public function setRadioTablesCount(?int $radioTablesCount): self
    {
        $this->radioTablesCount = $radioTablesCount;

        return $this;
    }

    public function getOldPassCompat(): ?bool
    {
        return $this->oldPassCompat;
    }

    public function setOldPassCompat(?bool $oldPassCompat): self
    {
        $this->oldPassCompat = $oldPassCompat;

        return $this;
    }
}
