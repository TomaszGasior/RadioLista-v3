<?php

namespace App\Util\Data;

use App\Entity\RadioStation;
use App\Entity\RadioTable;

trait RadioTableLabelsTrait
{
    protected function getFrequencyLabel(string $unit): string
    {
        return [
            RadioTable::FREQUENCY_MHZ => 'MHz',
            RadioTable::FREQUENCY_KHZ => 'kHz',
        ][$unit];
    }

    protected function getPowerLabel(): string
    {
        return 'kW';
    }

    protected function getDistanceLabel(): string
    {
        return 'km';
    }

    protected function getMaxSignalLevelLabel(string $unit): string
    {
        return [
            RadioTable::MAX_SIGNAL_LEVEL_DB => 'dB',
            RadioTable::MAX_SIGNAL_LEVEL_DBF => 'dBf',
            RadioTable::MAX_SIGNAL_LEVEL_DBUV => 'dBµV',
            RadioTable::MAX_SIGNAL_LEVEL_DBM => 'dBm',
        ][$unit];
    }

    protected function getPolarizationLabel(string $type): string
    {
        return [
            RadioStation::POLARIZATION_HORIZONTAL => 'H',
            RadioStation::POLARIZATION_VERTICAL => 'V',
            RadioStation::POLARIZATION_CIRCULAR => 'C',
            RadioStation::POLARIZATION_VARIOUS => 'M',
            RadioStation::POLARIZATION_NONE => '',
        ][$type];
    }

    protected function getQualityLabel(string $type): string
    {
        return [
            RadioStation::QUALITY_VERY_GOOD => '5',
            RadioStation::QUALITY_GOOD => '4',
            RadioStation::QUALITY_MIDDLE => '3',
            RadioStation::QUALITY_BAD => '2',
            RadioStation::QUALITY_VERY_BAD => '1',
        ][$type];
    }
}
