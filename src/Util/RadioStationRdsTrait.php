<?php

namespace App\Util;

trait RadioStationRdsTrait
{
    protected function alignRDSFrame(string $frame): string
    {
        $emptyChars = 8 - mb_strlen($frame);

        if ($emptyChars > 0) {
            $frame = str_repeat(' ', floor($emptyChars/2)) . $frame . str_repeat(' ', ceil($emptyChars/2));
        }
        elseif ($emptyChars < 0) {
            $frame = mb_substr($frame, 0, 8);
        }

        return $frame;
    }
}
