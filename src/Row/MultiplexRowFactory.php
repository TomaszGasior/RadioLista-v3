<?php

namespace App\Row;

use App\Entity\Enum\RadioStation\DabChannel;
use App\Entity\Multiplex;
use App\Model\Row;
use App\Model\RowEditRoute;
use RuntimeException;

class MultiplexRowFactory implements RowFactoryInterface
{
    public function create(object $object): Row
    {
        if (!$object instanceof Multiplex) {
            throw new RuntimeException;
        }

        $multiplex = $object;

        return new Row(
            name: $multiplex->getName(),
            radioGroup: null,
            country: $multiplex->getCountry(),
            region: null,
            frequency: $multiplex->getFrequency(),
            location: $multiplex->getLocation(),
            power: $multiplex->getPower(),
            polarization: $multiplex->getPolarization(),
            multiplex: null,
            dabChannel: DabChannel::from($multiplex->getFrequency()),
            distance: $multiplex->getDistance(),
            maxSignalLevel: $multiplex->getMaxSignalLevel(),
            reception: $multiplex->getReception(),
            privateNumber: null,
            firstLogDate: $multiplex->getFirstLogDate(),
            quality: $multiplex->getQuality(),
            type: null,
            rds: null,
            rdsPi: null,
            comment: $multiplex->getComment(),
            externalAnchor: $multiplex->getExternalAnchor(),

            appearance: clone $multiplex->getAppearance(),

            rowEditRoute: new RowEditRoute('multiplex.edit', [
                'id' => $multiplex->getId(),
                'radioTableId' => $multiplex->getRadioTable()->getId(),
            ]),
        );
    }

    public function supports(object $object): bool
    {
        return $object instanceof Multiplex;
    }
}
