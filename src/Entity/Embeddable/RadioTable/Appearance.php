<?php

namespace App\Entity\Embeddable\RadioTable;

use App\Validator\ClassConstantsChoice;
use App\Validator\HexColor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class Appearance
{
    const WIDTH_STANDARD = 1;
    const WIDTH_FULL = 2;
    const WIDTH_CUSTOM = 3;

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
     * @ORM\Column(type="smallint")
     * @ClassConstantsChoice(class=Appearance::class, prefix="WIDTH_")
     */
    private $widthType = self::WIDTH_STANDARD;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\GreaterThanOrEqual(900, message="radio_table.appearance.width_min_value")
     * @Assert\Expression(
     *     "value || this.getWidthType() !== width_custom",
     *     values={"width_custom": Appearance::WIDTH_CUSTOM},
     *     message="radio_table.appearance.width_required"
     * )
     */
    private $customWidth;

    /**
     * @ORM\Column(type="boolean")
     */
    private $collapsedComments = false;

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

    public function getWidthType(): ?int
    {
        return $this->widthType;
    }

    public function setWidthType(int $widthType): self
    {
        $this->widthType = $widthType;

        return $this;
    }

    public function getCustomWidth(): ?int
    {
        return $this->customWidth;
    }

    public function setCustomWidth(?int $customWidth): self
    {
        $this->customWidth = $customWidth;

        return $this;
    }

    public function getCollapsedComments(): ?bool
    {
        return $this->collapsedComments;
    }

    public function setCollapsedComments(bool $collapsedComments): self
    {
        $this->collapsedComments = $collapsedComments;

        return $this;
    }
}
