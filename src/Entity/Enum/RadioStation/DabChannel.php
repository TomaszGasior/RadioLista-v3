<?php

namespace App\Entity\Enum\RadioStation;

enum DabChannel: string
{
    case C_5A = '174.928';
    case C_5B = '176.640';
    case C_5C = '178.352';
    case C_5D = '180.064';
    case C_6A = '181.936';
    case C_6B = '183.648';
    case C_6C = '185.360';
    case C_6D = '187.072';
    case C_7A = '188.928';
    case C_7B = '190.640';
    case C_7C = '192.352';
    case C_7D = '194.064';
    case C_8A = '195.936';
    case C_8B = '197.648';
    case C_8C = '199.360';
    case C_8D = '201.072';
    case C_9A = '202.928';
    case C_9B = '204.640';
    case C_9C = '206.352';
    case C_9D = '208.064';
    case C_10A = '209.936';
    case C_10B = '211.648';
    case C_10C = '213.360';
    case C_10D = '215.072';
    case C_10N = '210.096';
    case C_11A = '216.928';
    case C_11B = '218.640';
    case C_11C = '220.352';
    case C_11D = '222.064';
    case C_11N = '217.088';
    case C_12A = '223.936';
    case C_12B = '225.648';
    case C_12C = '227.360';
    case C_12D = '229.072';
    case C_12N = '224.096';
    case C_13A = '230.784';
    case C_13B = '232.496';
    case C_13C = '234.208';
    case C_13D = '235.776';
    case C_13E = '237.488';
    case C_13F = '239.200';

    public function getLabel(): string
    {
        return substr($this->name, 2);
    }
}
