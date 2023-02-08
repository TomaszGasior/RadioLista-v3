<?php

namespace App\Entity\Embeddable\RadioStation;

use App\Entity\Enum\RadioStation\Background;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Appearance
{
    /**
     * @ORM\Column(type="smallint", enumType=Background::class, nullable=true)
     */
    private ?Background $background = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $bold = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $italic = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $strikethrough = false;

    public function getBackground(): ?Background
    {
        return $this->background;
    }

    public function setBackground(?Background $background): self
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
