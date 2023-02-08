<?php

namespace App\Entity\Enum\RadioStation;

enum Quality: int
{
    case VERY_GOOD = 5;
    case GOOD = 4;
    case MIDDLE = 3;
    case BAD = 2;
    case VERY_BAD = 1;
}
