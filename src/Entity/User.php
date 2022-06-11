<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\EntityListeners({"App\Doctrine\EntityListener\UserListener"})
 * @UniqueEntity("name", groups={"Default", "RedefinePassword"}, message="user.name_not_unique")
 * @ORM\Cache("NONSTRICT_READ_WRITE")
 */
class User implements UserInterface, LegacyPasswordAuthenticatedUserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\NotBlank(groups={"Default", "RedefinePassword"})
     * @Assert\Length(max=50, groups={"Default", "RedefinePassword"})
     * @Assert\Regex(
     *     "/^[a-zA-Z0-9_\.\-]*$/", groups={"Default", "RedefinePassword"},
     *     message="user.name_invalid_chars"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"Default"})
     */
    private $password;

    /**
     * @Assert\NotBlank(groups={"RedefinePassword"})
     * @Assert\Length(max=100, groups={"RedefinePassword"})
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
     * @ORM\Column(type="string", length=2000, nullable=true)
     * @Assert\Length(max=2000, groups={"Default", "RedefinePassword"})
     */
    private $aboutMe;

    /**
     * @ORM\Column(type="boolean")
     */
    private $publicProfile = false;

    /**
     * @ORM\Column(type="integer")
     */
    private $radioTablesCount = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $admin = false;

    public function __construct()
    {
        $this->lastActivityDate = new \DateTime;
        $this->registerDate = new \DateTime;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setPublicProfile(bool $publicProfile): self
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
     * Not persisted.
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * Not persisted. Used by entity listener for password encoding.
     */
    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): ?string
    {
        return $this->name;
    }

    /**
     * @see UserInterface
     */
    public function getUsername(): ?string
    {
        return $this->name;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Used only by legacy RLv1PasswordHasher for compatibility with RLv1.
     *
     * @see LegacyPasswordAuthenticatedUserInterface
     */
    public function getSalt(): ?string
    {
        return $this->registerDate->format('Y-m-d');
    }

    /**
     * Additional privileges for user with ROLE_ADMIN:
     * — use administration panel,
     * — preview private radio tables and hidden user profiles,
     * — browse application while maintenance mode is enabled.
     *
     * @see UserInterface
     */
    public function getRoles(): array
    {
        if ($this->admin) {
            return ['ROLE_USER', 'ROLE_ADMIN'];
        }

        return ['ROLE_USER'];
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }
}
