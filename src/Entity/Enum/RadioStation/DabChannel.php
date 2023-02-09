<?php

namespace App\Entity\Enum\RadioStation;

enum DabChannel: string
{
    case CHANNEL_5A = '5A';
    case CHANNEL_5B = '5B';
    case CHANNEL_5C = '5C';
    case CHANNEL_5D = '5D';
    case CHANNEL_6A = '6A';
    case CHANNEL_6B = '6B';
    case CHANNEL_6C = '6C';
    case CHANNEL_6D = '6D';
    case CHANNEL_7A = '7A';
    case CHANNEL_7B = '7B';
    case CHANNEL_7C = '7C';
    case CHANNEL_7D = '7D';
    case CHANNEL_8A = '8A';
    case CHANNEL_8B = '8B';
    case CHANNEL_8C = '8C';
    case CHANNEL_8D = '8D';
    case CHANNEL_9A = '9A';
    case CHANNEL_9B = '9B';
    case CHANNEL_9C = '9C';
    case CHANNEL_9D = '9D';
    case CHANNEL_10A = '10A';
    case CHANNEL_10B = '10B';
    case CHANNEL_10C = '10C';
    case CHANNEL_10D = '10D';
    case CHANNEL_10N = '10N';
    case CHANNEL_11A = '11A';
    case CHANNEL_11B = '11B';
    case CHANNEL_11C = '11C';
    case CHANNEL_11D = '11D';
    case CHANNEL_11N = '11N';
    case CHANNEL_12A = '12A';
    case CHANNEL_12B = '12B';
    case CHANNEL_12C = '12C';
    case CHANNEL_12D = '12D';
    case CHANNEL_12N = '12N';
    case CHANNEL_13A = '13A';
    case CHANNEL_13B = '13B';
    case CHANNEL_13C = '13C';
    case CHANNEL_13D = '13D';
    case CHANNEL_13E = '13E';
    case CHANNEL_13F = '13F';

    public function getFrequency(): string
    {
        return match ($this) {
            self::CHANNEL_5A => '174.928',
            self::CHANNEL_5B => '176.640',
            self::CHANNEL_5C => '178.352',
            self::CHANNEL_5D => '180.064',
            self::CHANNEL_6A => '181.936',
            self::CHANNEL_6B => '183.648',
            self::CHANNEL_6C => '185.360',
            self::CHANNEL_6D => '187.072',
            self::CHANNEL_7A => '188.928',
            self::CHANNEL_7B => '190.640',
            self::CHANNEL_7C => '192.352',
            self::CHANNEL_7D => '194.064',
            self::CHANNEL_8A => '195.936',
            self::CHANNEL_8B => '197.648',
            self::CHANNEL_8C => '199.360',
            self::CHANNEL_8D => '201.072',
            self::CHANNEL_9A => '202.928',
            self::CHANNEL_9B => '204.640',
            self::CHANNEL_9C => '206.352',
            self::CHANNEL_9D => '208.064',
            self::CHANNEL_10A => '209.936',
            self::CHANNEL_10B => '211.648',
            self::CHANNEL_10C => '213.360',
            self::CHANNEL_10D => '215.072',
            self::CHANNEL_10N => '210.096',
            self::CHANNEL_11A => '216.928',
            self::CHANNEL_11B => '218.640',
            self::CHANNEL_11C => '220.352',
            self::CHANNEL_11D => '222.064',
            self::CHANNEL_11N => '217.088',
            self::CHANNEL_12A => '223.936',
            self::CHANNEL_12B => '225.648',
            self::CHANNEL_12C => '227.360',
            self::CHANNEL_12D => '229.072',
            self::CHANNEL_12N => '224.096',
            self::CHANNEL_13A => '230.784',
            self::CHANNEL_13B => '232.496',
            self::CHANNEL_13C => '234.208',
            self::CHANNEL_13D => '235.776',
            self::CHANNEL_13E => '237.488',
            self::CHANNEL_13F => '239.200',
        };
    }
}
