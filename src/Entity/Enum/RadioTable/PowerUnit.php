<?php

namespace App\Entity\Enum\RadioTable;

enum PowerUnit: int
{
    case KW = 1;

    public function getLabel(): string
    {
        return match ($this) {
            self::KW => 'kW',
        };
    }
}
