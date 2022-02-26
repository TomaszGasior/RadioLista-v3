<?php

namespace App\Entity\Embeddable\RadioStation;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class Rds
{
    /**
     * Programme Service
     *
     * @ORM\Column(type="array")
     * @Assert\All({
     *     @Assert\Type("array"),
     *     @Assert\All({@Assert\Type("string")}),
     * })
     */
    private $ps = [];

    /**
     * Radio Text
     *
     * @ORM\Column(type="array")
     * @Assert\All({@Assert\Type("string")})
     */
    private $rt = [];

    /**
     * Programme Type

     * @ORM\Column(type="string", length=25, nullable=true)
     * @Assert\Length(max=25)
     */
    private $pty;

    /**
     * Programme Identification
     *
     * @ORM\Column(type="string", length=4, nullable=true)
     * @Assert\Length(max=4)
     */
    private $pi;

    public function getPs(): array
    {
        return $this->ps;
    }

    public function setPs(array $ps): self
    {
        $this->ps = $ps;

        return $this;
    }

    public function getRt(): array
    {
        return $this->rt;
    }

    public function setRt(array $rt): self
    {
        $this->rt = $rt;

        return $this;
    }

    public function getPty(): ?string
    {
        return $this->pty;
    }

    public function setPty(?string $pty): self
    {
        $this->pty = $pty;

        return $this;
    }

    public function getPi(): ?string
    {
        return $this->pi;
    }

    public function setPi(?string $pi): self
    {
        $this->pi = $pi;

        return $this;
    }
}
