<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Entity\RadioStation;

interface ExporterInterface
{
    /**
     * @param RadioStation[] $radioStations
     */
    public function render(RadioTable $radioTable, array $radioStations): string;
}
