<?php

namespace App\Entity\Enum\RadioStation;

enum Polarization: string
{
    case HORIZONTAL = 'H';
    case VERTICAL = 'V';
    case CIRCULAR = 'C';
    case VARIOUS = 'M';
}
