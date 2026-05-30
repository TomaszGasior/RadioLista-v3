<?php

namespace App\DigitalMigration;

class ConvertionPropertiesMapping
{
    public const array COPY_RADIO_STATION_TO_MULTIPLEX_DIRECTLY = [
        'Frequency',
        'Name',
        'Comment',
        'ExternalAnchor',
        'RadioGroup',
        'Region',
        'Country',
        'Location',
        'Power',
        'Polarization',
        'Distance',
        'MaxSignalLevel',
        'Reception',
        'FirstLogDate',
        'Quality',
        'Appearance',
    ];

    public const array COPY_RADIO_STATION_TO_MULTIPLEX_THROUGH_DIGITAL_RADIO_STATION_DEPENDENCY = [
        'Frequency',
        'Country',
        'Location',
        'Power',
        'Polarization',
        'Distance',
        'MaxSignalLevel',
        'Reception',
        'FirstLogDate',
        'Quality',
    ];

    public const array COPY_RADIO_STATION_TO_DIGITAL_RADIO_STATION = [
        'Name',
        'Comment',
        'ExternalAnchor',
        'RadioGroup',
        'Region',
        'PrivateNumber',
        'Type',
        'Rds',
        'Appearance',
    ];

    private function __construct() {}
}
