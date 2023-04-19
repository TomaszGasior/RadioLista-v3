<?php

namespace App\Entity;

use App\Doctrine\EntityListener\UserListener;
use App\Repository\UserRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\EntityListeners([UserListener::class])]
#[UniqueEntity('name', message: 'user.name_not_unique')]
#[ORM\Cache('NONSTRICT_READ_WRITE')]
class User implements UserInterface, LegacyPasswordAuthenticatedUserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 50, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[Assert\Regex('/^[a-zA-Z0-9_\.\-]*$/', message: 'user.name_invalid_chars')]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $password;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private DateTime $lastActivityDate;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private DateTime $registerDate;

    #[ORM\Column(type: Types::STRING, length: 2000, nullable: true)]
    #[Assert\Length(max: 2000)]
    private ?string $aboutMe = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $publicProfile = false;

    #[ORM\Column(type: Types::INTEGER)]
    private int $radioTablesCount = 0;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $admin = false;

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->lastActivityDate = new DateTime;
        $this->registerDate = new DateTime;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Throws an exception if setPassword() wasn't called yet.
     *
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getLastActivityDate(): DateTimeInterface
    {
        return $this->lastActivityDate;
    }

    public function refreshLastActivityDate(): self
    {
        $this->lastActivityDate = new DateTime;

        return $this;
    }

    public function getRegisterDate(): DateTimeInterface
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

    public function getPublicProfile(): bool
    {
        return $this->publicProfile;
    }

    public function setPublicProfile(bool $publicProfile): self
    {
        $this->publicProfile = $publicProfile;

        return $this;
    }

    public function getRadioTablesCount(): int
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
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->name;
    }

    /**
     * Used only by legacy RLv1PasswordHasher for compatibility with RLv1.
     *
     * @see LegacyPasswordAuthenticatedUserInterface
     */
    public function getSalt(): string
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
    }
}
