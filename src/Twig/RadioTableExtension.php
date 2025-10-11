<?php

namespace App\Twig;

use App\Util\RadioStationRdsTrait;
use Twig\Attribute\AsTwigFilter;

class RadioTableExtension
{
    use RadioStationRdsTrait {
        alignRDSFrame as doAlignRDSFrame;
    }

    #[AsTwigFilter('align_rds_frame')]
    public function alignRDSFrame(string $frame): string
    {
        return $this->doAlignRDSFrame($frame);
    }
}
