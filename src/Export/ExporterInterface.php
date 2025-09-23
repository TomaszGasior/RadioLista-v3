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
    public function render(string $format, RadioTable $radioTable, array $radioStations): string;

    public function supports(string $format): bool;
}
