<?php

namespace App\Entity\Embeddable\RadioTable;

use App\Entity\Enum\RadioTable\Width;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class Appearance
{
    #[ORM\Column(type: Types::SMALLINT, enumType: Width::class)]
    private Width $widthType = Width::STANDARD;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Assert\GreaterThanOrEqual(900, message: 'radio_table.appearance_width_min_value')]
    #[Assert\Expression(
        'value || this.getWidthType() !== width_custom',
        values: ['width_custom' => Width::CUSTOM],
        message: 'This value should not be blank.'
    )]
    private ?int $customWidth;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $collapsedComments = false;

    public function getWidthType(): Width
    {
        return $this->widthType;
    }

    public function setWidthType(Width $widthType): self
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
