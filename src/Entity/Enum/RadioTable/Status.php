<?php

namespace App\Entity\Enum\RadioTable;

enum Status: int
{
    case PUBLIC = 1;
    case UNLISTED = 0;
    case PRIVATE = -1;
}
