<?php

namespace App\Entity\Embeddable\RadioStation;

use App\Validator\ClassConstantsChoice;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class Appearance
{
    public const BACKGROUND_NONE = null;
    public const BACKGROUND_RED = 1;
    public const BACKGROUND_GREEN = 2;
    public const BACKGROUND_BLUE = 3;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @ClassConstantsChoice(class=Appearance::class, prefix="BACKGROUND_")
     */
    private $background = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private $bold = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $italic = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $strikethrough = false;

    public function getBackground(): ?int
    {
        return $this->background;
    }

    public function setBackground(?int $background): self
    {
        $this->background = $background;

        return $this;
    }

    public function isBold(): bool
    {
        return $this->bold;
    }

    public function setBold(bool $bold): self
    {
        $this->bold = $bold;

        return $this;
    }

    public function isItalic(): bool
    {
        return $this->italic;
    }

    public function setItalic(bool $italic): self
    {
        $this->italic = $italic;

        return $this;
    }

    public function isStrikethrough(): bool
    {
        return $this->strikethrough;
    }

    public function setStrikethrough(bool $strikethrough): self
    {
        $this->strikethrough = $strikethrough;

        return $this;
    }
}
