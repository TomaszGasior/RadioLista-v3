<?php

namespace App\Entity\Enum\RadioStation;

enum DabChannel: string
{
    case CH_5A = '174.928';
    case CH_5B = '176.640';
    case CH_5C = '178.352';
    case CH_5D = '180.064';
    case CH_6A = '181.936';
    case CH_6B = '183.648';
    case CH_6C = '185.360';
    case CH_6D = '187.072';
    case CH_7A = '188.928';
    case CH_7B = '190.640';
    case CH_7C = '192.352';
    case CH_7D = '194.064';
    case CH_8A = '195.936';
    case CH_8B = '197.648';
    case CH_8C = '199.360';
    case CH_8D = '201.072';
    case CH_9A = '202.928';
    case CH_9B = '204.640';
    case CH_9C = '206.352';
    case CH_9D = '208.064';
    case CH_10A = '209.936';
    case CH_10B = '211.648';
    case CH_10C = '213.360';
    case CH_10D = '215.072';
    case CH_10N = '210.096';
    case CH_11A = '216.928';
    case CH_11B = '218.640';
    case CH_11C = '220.352';
    case CH_11D = '222.064';
    case CH_11N = '217.088';
    case CH_12A = '223.936';
    case CH_12B = '225.648';
    case CH_12C = '227.360';
    case CH_12D = '229.072';
    case CH_12N = '224.096';
    case CH_13A = '230.784';
    case CH_13B = '232.496';
    case CH_13C = '234.208';
    case CH_13D = '235.776';
    case CH_13E = '237.488';
    case CH_13F = '239.200';

    public function getLabel(): string
    {
        return substr($this->name, 3);
    }

    static public function getByFrequency(string $frequency): ?DabChannel
    {
        $frequency = intval($frequency);

        foreach (self::cases() as $dabChannel) {
            if (intval($dabChannel->value) === $frequency) {
                return $dabChannel;
            }
        }

        return null;
    }
}
