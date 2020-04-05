<?php

namespace App\Twig;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class RadioTableExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_frequency_label', [$this, 'getFrequencyLabel']),
            new TwigFunction('get_max_signal_level_label', [$this, 'getMaxSignalLevelLabel']),
            new TwigFunction('get_polarization_label', [$this, 'getPolarizationLabel']),
            new TwigFunction('get_quality_label', [$this, 'getQualityLabel']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('align_rds_frame', [$this, 'alignRDSFrame']),
        ];
    }

    public function getFrequencyLabel(string $unit): string
    {
        return [
            RadioTable::FREQUENCY_MHZ => 'MHz',
            RadioTable::FREQUENCY_KHZ => 'kHz',
        ][$unit];
    }

    public function getMaxSignalLevelLabel(string $unit): string
    {
        return [
            RadioTable::MAX_SIGNAL_LEVEL_DB => 'dB',
            RadioTable::MAX_SIGNAL_LEVEL_DBF => 'dBf',
            RadioTable::MAX_SIGNAL_LEVEL_DBUV => 'dBµV',
            RadioTable::MAX_SIGNAL_LEVEL_DBM => 'dBm',
        ][$unit];
    }

    public function getPolarizationLabel(string $type): string
    {
        return [
            RadioStation::POLARIZATION_HORIZONTAL => 'H',
            RadioStation::POLARIZATION_VERTICAL => 'V',
            RadioStation::POLARIZATION_CIRCULAR => 'C',
            RadioStation::POLARIZATION_VARIOUS => 'M',
            RadioStation::POLARIZATION_NONE => '',
        ][$type];
    }

    public function getQualityLabel(string $type): string
    {
        return [
            RadioStation::QUALITY_VERY_GOOD => '5',
            RadioStation::QUALITY_GOOD => '4',
            RadioStation::QUALITY_MIDDLE => '3',
            RadioStation::QUALITY_BAD => '2',
            RadioStation::QUALITY_VERY_BAD => '1',
        ][$type];
    }

    public function alignRDSFrame(string $frame): string
    {
        $emptyChars = 8 - mb_strlen($frame);

        if ($emptyChars > 0) {
            $frame = str_repeat(' ', floor($emptyChars/2)) . $frame . str_repeat(' ', ceil($emptyChars/2));
        }
        elseif ($emptyChars < 0) {
            $frame = substr($frame, 0, 8);
        }

        return $frame;
    }
}
