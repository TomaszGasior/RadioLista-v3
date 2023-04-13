<?php

namespace App\Entity\Enum\RadioTable;

enum DistanceUnit: int
{
    case KM = 1;

    public function getLabel(): string
    {
        return match ($this) {
            self::KM => 'km',
        };
    }
}
