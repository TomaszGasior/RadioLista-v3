<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Enum\ExportFormat;
use App\Model\Row;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface ExporterInterface
{
    /**
     * @param Row[] $rows
     */
    public function render(ExportFormat $format, RadioTable $radioTable, array $rows): string;

    public function supports(ExportFormat $format): bool;
}
