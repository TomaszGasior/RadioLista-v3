<?php

namespace App\Util\Data;

trait RadioTableLabelsTrait
{
    protected function getPowerLabel(): string
    {
        return 'kW';
    }

    protected function getDistanceLabel(): string
    {
        return 'km';
    }
}
