<?php

namespace App\Entity\Enum\RadioTable;

/**
 * * Value of each enum case must be maching name of getter method
 *   in RadioStation entity, without "get" prefix.
 *   Radio table rendering code may fall back to calling "get" + {enum value}
 *   method on RadioStation entity.
 *   Also, Twig templates hardcode column names for convenience.
 *
 * * Cases order defines order of disabled columns in columns settings page.
 */
enum Column: string
{
    case FREQUENCY = 'frequency';
    case NAME = 'name';
    case LOCATION = 'location';
    case POWER = 'power';
    case POLARIZATION = 'polarization';
    case MULTIPLEX = 'multiplex';
    case DAB_CHANNEL = 'dabChannel';
    case COUNTRY = 'country';
    case REGION = 'region';
    case QUALITY = 'quality';
    case RDS = 'rds';
    case FIRST_LOG_DATE = 'firstLogDate';
    case RECEPTION = 'reception';
    case DISTANCE = 'distance';
    case MAX_SIGNAL_LEVEL = 'maxSignalLevel';
    case RDS_PI = 'rdsPi';
    case RADIO_GROUP = 'radioGroup';
    case TYPE = 'type';
    case PRIVATE_NUMBER = 'privateNumber';
    case COMMENT = 'comment';
    case EXTERNAL_ANCHOR = 'externalAnchor';

    /**
     * @return Column[]
     */
    static public function getSortable(): array
    {
        return [
            Column::FREQUENCY,
            Column::NAME,
            Column::PRIVATE_NUMBER,
        ];
    }
}
