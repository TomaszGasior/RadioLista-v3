<?php

namespace App\DigitalMigration;

enum ConvertionDecision
{
    case CONVERT_RADIO_STATION_TO_MULTIPLEX;
    case CONVERT_RADIO_STATION_TO_DIGITAL_RADIO_STATION;
}
