<?php

namespace App\Twig;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Util\RadioStationRdsTrait;
use App\Util\Data\RadioTableLabelsTrait;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class RadioTableExtension extends AbstractExtension
{
    use RadioTableLabelsTrait {
        getPowerLabel as public;
        getDistanceLabel as public;
    }

    use RadioStationRdsTrait {
        alignRDSFrame as public;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_power_label', [$this, 'getPowerLabel']),
            new TwigFunction('get_distance_label', [$this, 'getDistanceLabel']),
        ];
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
