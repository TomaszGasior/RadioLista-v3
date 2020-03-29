<?php

namespace App\Entity\Embeddable\RadioTable;

use App\Validator\HexColor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class Appearance
{
    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $theme;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @HexColor(message="radio_table.appearance.fg_invalid")
     */
    private $textColor;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @HexColor(message="radio_table.appearance.bg_invalid")
     */
    private $backgroundColor;

    /**
     * @ORM\Column(type="string", length=400, nullable=true)
     * @Assert\Length(max=400, maxMessage="radio_table.appearance.img_max_length")
     * @Assert\Url(message="radio_table.appearance.img_invalid")
     */
    private $backgroundImage;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $fullWidth = false;

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    public function setTextColor(?string $textColor): self
    {
        $this->textColor = $textColor;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(?string $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    public function getBackgroundImage(): ?string
    {
        return $this->backgroundImage;
    }

    public function setBackgroundImage(?string $backgroundImage): self
    {
        $this->backgroundImage = $backgroundImage;

        return $this;
    }

    public function isFullWidth(): bool
    {
        return $this->fullWidth;
    }

    public function setFullWidth(bool $fullWidth): self
    {
        $this->fullWidth = $fullWidth;

        return $this;
    }
}
