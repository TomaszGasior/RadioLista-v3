<?php

namespace App\Entity\Enum\RadioTable;

enum MaxSignalLevelUnit: int
{
    case DB = 1;
    case DBF = 2;
    case DBUV = 3;
    case DBM = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::DB => 'dB',
            self::DBF => 'dBf',
            self::DBUV => 'dBµV',
            self::DBM => 'dBm',
        };
    }
}
