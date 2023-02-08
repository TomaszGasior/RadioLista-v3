<?php

namespace App\Entity\Enum\RadioTable;

enum MaxSignalLevelUnit: int
{
    case DB = 1;
    case DBF = 2;
    case DBUV = 3;
    case DBM = 4;
}
