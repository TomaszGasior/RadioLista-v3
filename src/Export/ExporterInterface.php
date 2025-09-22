<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Entity\RadioStation;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface ExporterInterface
{
    /**
     * @param RadioStation[] $radioStations
     */
    public function render(RadioTable $radioTable, array $radioStations): string;
}
