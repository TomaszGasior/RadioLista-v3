<?php

namespace App\DigitalMigration;

use App\Entity\Enum\RadioStation\DabChannel;
use App\Entity\Enum\RadioTable\FrequencyUnit;
use App\Entity\RadioStation;

class ConvertionDecisionMaker
{
    public function decide(RadioStation $radioStation): ?ConvertionDecision
    {
        if (
            $this->radioStationNameLooksLikeMultiplex($radioStation->getName())
            || (!$radioStation->getMultiplex() && $radioStation->getDabChannel())
            || (!$radioStation->getMultiplex() && $this->radioStationFrequencyLooksLikeMultiplex($radioStation))
        ) {
            return ConvertionDecision::CONVERT_RADIO_STATION_TO_MULTIPLEX;
        }
        elseif ($radioStation->getMultiplex()) {
            return ConvertionDecision::CONVERT_RADIO_STATION_TO_DIGITAL_RADIO_STATION;
        }

        return null;
    }

    private function radioStationNameLooksLikeMultiplex(string $name): bool
    {
        $strings = [
            'MUX',
            'DAB ', // trailing space
            'DAB+ ', // trailing space
            'dvb',
            'multiplex',
            'multipleks',
        ];

        foreach ($strings as $string) {
            if (str_contains(mb_strtolower($name), mb_strtolower($string))) {
                return true;
            }
        }

        return false;
    }

    private function radioStationFrequencyLooksLikeMultiplex(RadioStation $radioStation): bool
    {
        $radioTable = $radioStation->getRadioTable();

        if ($radioTable->getFrequencyUnit() !== FrequencyUnit::MHZ) {
            return false;
        }

        if ($this->radioTableNameLooksLikeTelevisionTable($radioTable->getName())) {
            return false;
        }

        $dabChannel = DabChannel::getByFrequency($radioStation->getFrequency());

        return $dabChannel !== null;
    }

    private function radioTableNameLooksLikeTelevisionTable(string $name): bool
    {
        $strings = [
            '[TV]',
            'TV-',
            'TV ',
        ];

        foreach ($strings as $string) {
            if (str_contains($name, $string)) {
                return true;
            }
        }

        return false;
    }
}
