<?php

namespace App\Row;

use App\Entity\RadioStation;
use App\Model\Row;
use App\Model\RowEditRoute;
use RuntimeException;

class RadioStationRowFactory implements RowFactoryInterface
{
    public function create(object $object): Row
    {
        if (!$object instanceof RadioStation) {
            throw new RuntimeException;
        }

        $radioStation = $object;

        return new Row(
            name: $radioStation->getName(),
            radioGroup: $radioStation->getRadioGroup(),
            country: $radioStation->getCountry(),
            region: $radioStation->getRegion(),
            frequency: $radioStation->getFrequency(),
            location: $radioStation->getLocation(),
            power: $radioStation->getPower(),
            polarization: $radioStation->getPolarization(),
            multiplex: null,
            dabChannel: null,
            distance: $radioStation->getDistance(),
            maxSignalLevel: $radioStation->getMaxSignalLevel(),
            reception: $radioStation->getReception(),
            privateNumber: $radioStation->getPrivateNumber(),
            firstLogDate: $radioStation->getFirstLogDate(),
            quality: $radioStation->getQuality(),
            type: $radioStation->getType(),
            rds: clone $radioStation->getRds(),
            rdsPi: $radioStation->getRds()->getPi(),
            comment: $radioStation->getComment(),
            externalAnchor: $radioStation->getExternalAnchor(),

            appearance: clone $radioStation->getAppearance(),

            rowEditRoute: new RowEditRoute('radio_station.edit', [
                'id' => $radioStation->getId(),
                'radioTableId' => $radioStation->getRadioTable()->getId(),
            ]),
        );
    }

    public function supports(object $object): bool
    {
        return $object instanceof RadioStation;
    }
}
