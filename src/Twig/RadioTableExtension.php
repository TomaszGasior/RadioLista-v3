<?php

namespace App\Twig;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Util\Data\RadioStationRdsTrait;
use App\Util\Data\RadioTableLabelsTrait;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class RadioTableExtension extends AbstractExtension
{
    use RadioTableLabelsTrait {
        getFrequencyLabel as public;
        getPowerLabel as public;
        getDistanceLabel as public;
        getMaxSignalLevelLabel as public;
        getPolarizationLabel as public;
        getQualityLabel as public;
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
            new TwigFunction('get_frequency_label', [$this, 'getFrequencyLabel']),
            new TwigFunction('get_power_label', [$this, 'getPowerLabel']),
            new TwigFunction('get_distance_label', [$this, 'getDistanceLabel']),
            new TwigFunction('get_max_signal_level_label', [$this, 'getMaxSignalLevelLabel']),
            new TwigFunction('get_polarization_label', [$this, 'getPolarizationLabel']),
            new TwigFunction('get_quality_label', [$this, 'getQualityLabel']),
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
