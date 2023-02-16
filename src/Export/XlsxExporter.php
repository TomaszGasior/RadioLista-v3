<?php

namespace App\Export;

use App\Entity\RadioTable;
use App\Util\PhpSpreadsheetRenderer;

class XlsxExporter implements ExporterInterface
{
    public function __construct(private PhpSpreadsheetRenderer $phpSpreadsheetRenderer) {}

    /**
     * @param RadioStation[] $radioStations
     */
    public function render(RadioTable $radioTable, array $radioStations): string
    {
        return $this->phpSpreadsheetRenderer->render('Xlsx', $radioTable, $radioStations);
    }
}
