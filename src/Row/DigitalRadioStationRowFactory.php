<?php

namespace App\Row;

use App\Entity\DigitalRadioStation;
use App\Entity\Enum\RadioStation\DabChannel;
use App\Model\Row;
use App\Model\RowEditRoute;
use RuntimeException;

class DigitalRadioStationRowFactory implements RowFactoryInterface
{
    public function create(object $object): Row
    {
        if (!$object instanceof DigitalRadioStation) {
            throw new RuntimeException;
        }

        $digitalRadioStation = $object;

        return new Row(
            name: $digitalRadioStation->getName(),
            radioGroup: $digitalRadioStation->getRadioGroup(),
            country: $digitalRadioStation->getMultiplex()->getCountry(),
            region: $digitalRadioStation->getRegion(),
            frequency: $digitalRadioStation->getMultiplex()->getFrequency(),
            location: $digitalRadioStation->getMultiplex()->getLocation(),
            power: $digitalRadioStation->getMultiplex()->getPower(),
            polarization: $digitalRadioStation->getMultiplex()->getPolarization(),
            multiplex: $digitalRadioStation->getMultiplex()->getName(),
            dabChannel: DabChannel::from($digitalRadioStation->getMultiplex()->getFrequency()),
            distance: $digitalRadioStation->getMultiplex()->getDistance(),
            maxSignalLevel: $digitalRadioStation->getMultiplex()->getMaxSignalLevel(),
            reception: $digitalRadioStation->getMultiplex()->getReception(),
            privateNumber: $digitalRadioStation->getPrivateNumber(),
            firstLogDate: $digitalRadioStation->getMultiplex()->getFirstLogDate(),
            quality: $digitalRadioStation->getMultiplex()->getQuality(),
            type: $digitalRadioStation->getType(),
            rds: null,
            rdsPi: null,
            comment: $digitalRadioStation->getComment(),
            externalAnchor: $digitalRadioStation->getExternalAnchor(),

            appearance: clone $digitalRadioStation->getAppearance(),

            rowEditRoute: new RowEditRoute('digital_radio_station.edit', [
                'id' => $digitalRadioStation->getId(),
                'multiplexId' => $digitalRadioStation->getMultiplex()->getId(),
                'radioTableId' => $digitalRadioStation->getMultiplex()->getRadioTable()->getId(),
            ]),
        );
    }

    public function supports(object $object): bool
    {
        return $object instanceof DigitalRadioStation;
    }
}
