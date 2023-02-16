<?php

namespace App\Entity\Enum\RadioStation;

enum Reception: int
{
    case REGULAR = 0;
    case TROPO = 1;
    case SCATTER = 2;
    case SPORADIC_E = 3;
}
