<?php

namespace App\Twig;

use App\Util\RadioStationRdsTrait;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class RadioTableExtension extends AbstractExtension
{
    use RadioStationRdsTrait {
        alignRDSFrame as public;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('align_rds_frame', [$this, 'alignRDSFrame']),
        ];
    }
}
