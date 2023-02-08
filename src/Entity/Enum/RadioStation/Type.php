<?php

namespace App\Entity\Enum\RadioStation;

enum Type: int
{
    case MUSIC = 1;
    case INFORMATION = 2;
    case UNIVERSAL = 3;
    case RELIGIOUS = 4;
    case OTHER = 0;
}
