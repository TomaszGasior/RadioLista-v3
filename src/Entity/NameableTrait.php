<?php

namespace App\Entity;

use App\Entity\Embeddable\RadioStation\Appearance;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait NameableTrait
{
    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 500, nullable: true)]
    #[Assert\Length(max: 500)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::STRING, length: 300, nullable: true)]
    #[Assert\Length(max: 500)]
    #[Assert\Url(requireTld: false)]
    private ?string $externalAnchor = null;

    #[ORM\Embedded(class: Appearance::class)]
    #[Assert\Valid]
    private Appearance $appearance;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getExternalAnchor(): ?string
    {
        return $this->externalAnchor;
    }

    public function setExternalAnchor(?string $externalAnchor): self
    {
        $this->externalAnchor = $externalAnchor;

        return $this;
    }

    public function getAppearance(): Appearance
    {
        return $this->appearance;
    }
}
