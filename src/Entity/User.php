<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="Users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable, EncoderAwareInterface
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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RadioTable", mappedBy="owner", orphanRemoval=true)
     */
    private $radioTables;

    public function __construct()
    {
        $this->radioTables = new ArrayCollection();
    }

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

        if ($this->oldPassCompat) {
            $this->oldPassCompat = false;
        }

        return $this;
    }

    public function getLastActivityDate(): ?\DateTimeInterface
    {
        return $this->lastActivityDate;
    }

    public function getRegisterDate(): ?\DateTimeInterface
    {
        return $this->registerDate;
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

    /**
     * @return Collection|RadioTable[]
     */
    public function getRadioTables(): Collection
    {
        return $this->radioTables;
    }

    // UserInterface

    public function getUsername(): string
    {
        return $this->name;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
    }

    // Serializable

    public function serialize(): string
    {
        return serialize([
            $this->id,
            $this->name,
            $this->password,
        ]);
    }

    public function unserialize($serialized): void
    {
        list(
            $this->id,
            $this->name,
            $this->password,
        ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    // EncoderAwareInterface

    public function getEncoderName(): string
    {
        if ($this->oldPassCompat) {
            return 'rl_v1';
        }

        return 'rl_v2';
    }
}
