<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\EntityListeners({"App\Doctrine\EntityListener\UserListener"})
 * @UniqueEntity(
 *     "name", groups={"Default", "RedefinePassword"},
 *     message="Wybrana nazwa użytkownika jest już zajęta."
 * )
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
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\NotBlank(groups={"Default", "RedefinePassword"}, message="Nazwa użytkownika nie może być pusta.")
     * @Assert\Length(
     *     max=50, groups={"Default", "RedefinePassword"},
     *     maxMessage="Nazwa użytkownika może mieć maksymalnie 50 znaków.",
     * )
     * @Assert\Regex(
     *     "/^[a-zA-Z0-9_\.\-]*$/",
     *     groups={"Default", "RedefinePassword"},
     *     message="Nazwa użytkownika zawiera niedozwolone znaki.",
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(groups={"Default"})
     */
    private $password;

    /**
     * @Assert\NotBlank(groups={"RedefinePassword"}, message="Hasło nie może być puste.")
     * @Assert\Length(
     *     max=100, min=10, groups={"RedefinePassword"},
     *     minMessage="Hasło musi mieć co najmniej 10 znaków.",
     *     maxMessage="Hasło może mieć maksymalnie 100 znaków.",
     * )
     */
    private $plainPassword;

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
     * @Assert\Length(max=1000, groups={"Default", "RedefinePassword"})
     */
    private $aboutMe;

    /**
     * @ORM\Column(type="boolean")
     */
    private $publicProfile = false;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $radioTablesCount = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $oldPassCompat = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RadioTable", mappedBy="owner", orphanRemoval=true)
     * @ORM\OrderBy({"radioStationsCount" = "DESC"})
     */
    private $radioTables;

    public function __construct()
    {
        $this->lastActivityDate = new \DateTime;
        $this->registerDate = new \DateTime;

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

    public function setPasswordHash(string $passwordHash): self
    {
        $this->password = $passwordHash;

        return $this;
    }

    public function getLastActivityDate(): ?\DateTimeInterface
    {
        return $this->lastActivityDate;
    }

    public function refreshLastActivityDate(): self
    {
        $this->lastActivityDate = new \DateTime;

        return $this;
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

    public function increaseRadioTablesCount(): self
    {
        ++$this->radioTablesCount;

        return $this;
    }

    public function decreaseRadioTablesCount(): self
    {
        --$this->radioTablesCount;

        return $this;
    }

    /**
     * @return Collection|RadioTable[]
     */
    public function getRadioTables(): Collection
    {
        return $this->radioTables;
    }

    // Not persisted

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        if ($this->oldPassCompat) {
            $this->oldPassCompat = false;
        }

        return $this;
    }

    // UserInterface

    public function getUsername(): ?string
    {
        return $this->name;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        if ($this->oldPassCompat) {
            return $this->registerDate->format('Y-m-d');
        }

        return null;
    }

    public function getRoles(): array
    {
        if (1 === $this->id) {
            return ['ROLE_USER', 'ROLE_ADMIN'];
        }

        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    // Serializable

    public function serialize(): string
    {
        return serialize([
            $this->id,
            $this->name,
            $this->password,

            $this->registerDate,
            $this->oldPassCompat
        ]);
    }

    public function unserialize($serialized): void
    {
        list(
            $this->id,
            $this->name,
            $this->password,

            $this->registerDate,
            $this->oldPassCompat
        ) = unserialize($serialized, ['allowed_classes' => ['DateTime']]);
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
