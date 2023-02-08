<?php

namespace App\Util\Data;

use App\Entity\Enum\RadioStation\Polarization;
use App\Entity\Enum\RadioStation\Quality;
use App\Entity\Enum\RadioTable\FrequencyUnit;
use App\Entity\Enum\RadioTable\MaxSignalLevelUnit;

trait RadioTableLabelsTrait
{
    protected function getFrequencyLabel(FrequencyUnit $frequencyUnit): string
    {
        return match ($frequencyUnit) {
            FrequencyUnit::MHZ => 'MHz',
            FrequencyUnit::KHZ => 'kHz',
        };
    }

    protected function getPowerLabel(): string
    {
        return 'kW';
    }

    protected function getDistanceLabel(): string
    {
        return 'km';
    }

    protected function getMaxSignalLevelLabel(MaxSignalLevelUnit $maxSignalLevelUnit): string
    {
        return match ($maxSignalLevelUnit) {
            MaxSignalLevelUnit::DB => 'dB',
            MaxSignalLevelUnit::DBF => 'dBf',
            MaxSignalLevelUnit::DBUV => 'dBµV',
            MaxSignalLevelUnit::DBM => 'dBm',
        };
    }

    protected function getPolarizationLabel(Polarization $polarization): string
    {
        return match ($polarization) {
            Polarization::HORIZONTAL => 'H',
            Polarization::VERTICAL => 'V',
            Polarization::CIRCULAR => 'C',
            Polarization::VARIOUS => 'M',
        };
    }

    protected function getQualityLabel(Quality $quality): string
    {
        return match ($quality) {
            Quality::VERY_GOOD => '5',
            Quality::GOOD => '4',
            Quality::MIDDLE => '3',
            Quality::BAD => '2',
            Quality::VERY_BAD => '1',
        };
    }
}
