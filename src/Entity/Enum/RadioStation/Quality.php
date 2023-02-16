<?php

namespace App\Entity\Enum\RadioStation;

enum Quality: int
{
    case VERY_GOOD = 5;
    case GOOD = 4;
    case MIDDLE = 3;
    case BAD = 2;
    case VERY_BAD = 1;

    public function getLabel(): string
    {
        return match ($this) {
            self::VERY_GOOD => '5',
            self::GOOD => '4',
            self::MIDDLE => '3',
            self::BAD => '2',
            self::VERY_BAD => '1',
        };
    }
}
