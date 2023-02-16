<?php

namespace App\Entity\Enum\RadioStation;

enum Polarization: string
{
    case HORIZONTAL = 'H';
    case VERTICAL = 'V';
    case CIRCULAR = 'C';
    case VARIOUS = 'M';

    public function getLabel(): string
    {
        return match ($this) {
            self::HORIZONTAL => 'H',
            self::VERTICAL => 'V',
            self::CIRCULAR => 'C',
            self::VARIOUS => 'M',
        };
    }
}
