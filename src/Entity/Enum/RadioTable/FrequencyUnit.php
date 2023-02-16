<?php

namespace App\Entity\Enum\RadioTable;

enum FrequencyUnit: int
{
    case MHZ = 1;
    case KHZ = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::MHZ => 'MHz',
            self::KHZ => 'kHz',
        };
    }
}
