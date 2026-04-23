<?php

namespace App\Entity\Enum\RadioTable;

enum DigitalType: int
{
    case DISABLED = 0;
    case RADIO_STATIONS_SEPARATELY = 1;
    case MULTIPLEXES_MERGED = 2;
}
